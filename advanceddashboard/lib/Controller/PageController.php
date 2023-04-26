<?php
namespace OCA\AdvancedDashboard\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCP\ISession;
use OCP\User\Session\UserSession;
use OCP\IConfig;
use OCP\IUserManager;
use OCP\Files\FileInfo;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\Node;
use OCP\Files\IRootFolder;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IDBConnection;

use OCP\Share\IShare;
use OCP\Share\IManager;

use OCP\AppFramework\Http\Request;
use OCP\AppFramework\Http\RedirectResponse;


class PageController extends Controller {
	private $userId;

	private $config;
    private $userManager;
	private $rootFolder;
	private $session;
	private $timeFactory;
	private $connection;

	private $sessionManager;

	public function __construct(
		$AppName,
		IRequest $request,
		$UserId,
		IConfig $config,
		IUserManager $userManager,
		IRootFolder $rootFolder,
		ISession $session,
		ITimeFactory $timeFactory,
		IDBConnection $connection
		){

		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		
		$this->config = $config;
        $this->userManager = $userManager;
		$this->rootFolder = $rootFolder;
		$this->session = $session;
		$this->timeFactory = $timeFactory;
		$this->connection = $connection;

	}

	public function getFilesCountInDirectory($elements)
	{
		$count = 0;

		foreach ($elements as $content) {

			if ($content instanceof File) {
				$count++;
			}

			if ($content instanceof Folder) {
				$count += $this->getFilesCountInDirectory($content->getDirectoryListing());
			}
		}

		return $count;
	}

	
	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(){

		$users = $this->userManager->search('');
        $userQuotas = [];
		$db = \OC::$server->getDatabaseConnection();

        foreach ($users as $user) {
            $uid = $user->getUID();
            $quota = $this->config->getUserValue($uid, 'files', 'quota');

            $usedSpace = 0;
            $filecount = 0;
			$linkscount = 0;
			$linkscountbayemail = 0;
			
            $userFolder = $this->rootFolder->getUserFolder($user->getUID());
            $rootFolderContents = $userFolder->getDirectoryListing();

			$count = $this->getFilesCountInDirectory($rootFolderContents);

            foreach ($rootFolderContents as $content) {
                if ($content instanceof FileInfo) {
                    $usedSpace += $content->getSize();
                    $filecount++;
                }
            }

			
			$query = $db->prepare('SELECT count(*) FROM oc_share WHERE uid_owner = ? AND share_type = 3');
			$query->bindValue(1, $uid);
			$query->execute();
			$linkscount = $query->fetchColumn();
			
			$sqlstate = $db->prepare('SELECT COUNT(*) FROM oc_share WHERE uid_owner = ? AND share_type = 0');
			$sqlstate->bindValue(1, $uid);
			$sqlstate->execute();
			$linkscountbayemail = $sqlstate->fetchColumn();
            $userQuotas[$uid] = [
                'displayName' => $user->getDisplayName(),
                'usedSpace' => $usedSpace,
                'filecount' => $count,
				'linkscount' => $linkscount,
				'linkscountbayemail' => $linkscountbayemail,
            ];

			$topUsersByFilesCount[$uid] = [
                'displayName' => $user->getDisplayName(),
                'filecount' => $count,
            ];

			$topUsersByUsedSpace[$uid] = [
                'displayName' => $user->getDisplayName(),
                'usedSpace' => $usedSpace,
            ];
			
        }
		$sqlSahredFiles = $db->prepare('SELECT COUNT(*) FROM oc_share WHERE share_type = 3');
		$sqlSahredFiles->execute();
		$totalSahredFiles = $sqlSahredFiles->fetchColumn();

		$sqlInternalShares = $db->prepare('SELECT COUNT(*) FROM oc_share WHERE share_type = 0');
		$sqlInternalShares->execute();
		$totalInternalShares = $sqlInternalShares->fetchColumn();

		$this->sortByKey($topUsersByFilesCount, 'filecount', true);

		$this->sortByKey($topUsersByUsedSpace, 'usedSpace', true);

		// echo "<pre>";
		// var_dump($totalSahredFiles);
		// echo "</pre>";
		return new TemplateResponse('advanceddashboard', 'index', [
			'data'=>$userQuotas,
			'topUsersByFilesCount' => $topUsersByFilesCount,
			'topUsersByUsedSpace' => $topUsersByUsedSpace,
			'totalSahredFiles' => $totalSahredFiles,
			'totalInternalShares'=>$totalInternalShares
		]);
	}

	private function sortByKey(&$array, $key, $reverse = false, $limit = 20) {
		$column = [];
		foreach($array as $v) {
			$column[] = $v[$key] ?? null;
		}
		$order_flag = $reverse ? SORT_DESC : SORT_ASC;
		array_multisort($column, $order_flag, $array);

		$array = array_slice($array, 0, $limit);
	}
	
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function postactive(){
		$url = "/apps/advanceddashboard/usersactive";
		$start_time_str = $_POST['start_date'] ?? '';
		$end_time_str = $_POST['end_date'] ?? '';

		// Set default values to last 24 hours
		if (empty($start_time_str) && empty($end_time_str)) {			
			return new RedirectResponse($url);			
		} else {
			// Convert to timestamps
			$start_time = strtotime($start_time_str);
			$end_time = strtotime($end_time_str);
		}
		if ($start_time === false || $end_time === false) {
			// Invalid input, handle error
			die("Invalid start or end time");
		}
		// Prepare and execute SQL query
		$query = \OC::$server->getDatabaseConnection()->prepare("
			SELECT COUNT(DISTINCT uid) as active_users
			FROM oc_authtoken
			WHERE last_activity BETWEEN :start_time AND :end_time
		");
		$query->bindValue(':start_time', $start_time);
		$query->bindValue(':end_time', $end_time);
		$query->execute();
		$result = $query->fetch();
		$query->closeCursor();

		// Pass data to template
		$active_users = $result['active_users'];

		return new TemplateResponse('advanceddashboard', 'active', [
			'active_users' => $active_users,
			'start_time' => $start_time,
			'end_time' => $end_time
		]);
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */

	public function active(){

		// Get user input
		$start_time_str = $_POST['start_date'] ?? '';
		$end_time_str = $_POST['end_date'] ?? '';

		// Set default values to last 24 hours
		if (empty($start_time_str) && empty($end_time_str)) {
			$end_time = time(); // current time
			$start_time = strtotime('-24 hours', $end_time);
		} else {
			// Convert to timestamps
			$start_time = strtotime($start_time_str);
			$end_time = strtotime($end_time_str);
		}

		if ($start_time === false || $end_time === false) {
			// Invalid input, handle error
			die("Invalid start or end time");
		}

		// Prepare and execute SQL query
		$query = \OC::$server->getDatabaseConnection()->prepare("
			SELECT COUNT(DISTINCT uid) as active_users
			FROM oc_authtoken
			WHERE last_activity BETWEEN :start_time AND :end_time
		");
		$query->bindValue(':start_time', $start_time);
		$query->bindValue(':end_time', $end_time);
		$query->execute();
		$result = $query->fetch();
		$query->closeCursor();

		// Pass data to template
		$active_users = $result['active_users'];
		return new TemplateResponse('advanceddashboard', 'active', [
			'active_users' => $active_users,
			'start_time' => $start_time,
			'end_time' => $end_time
		]);


	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function filesByTime(){

		$userFilesByTime = '';
		
		return new TemplateResponse('advanceddashboard','filesbytime',['users'=>$userFilesByTime]);
	}

	public function getFilesCountInTimeRange($elements, int $startDate, int $endDate)
	{
		$count = 0;

		foreach ($elements as $content) {
			if ($content->getUploadTime() <= $endDate && $content->getUploadTime() >= $startDate) {
				$count++;
			}
			
			if ($content instanceof Folder) {
				$count += $this->getFilesCountInTimeRange($content->getDirectoryListing(), $startDate, $endDate);
			}
		}
		return $count;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */  

	public function filesByTimePOST(){
		$db = \OC::$server->getDatabaseConnection();
		$url="/apps/advanceddashboard/filesbytime";
		$start_time_str = $_POST['start_date'] ?? '';
		$end_time_str = $_POST['end_date'] ?? '';
		
		if (empty($start_time_str) && empty($end_time_str)) {
			// Invalid input, handle error
			return new RedirectResponse($url);
		} else {
			// Convert to timestamps
			$start_time = strtotime($start_time_str);
			$end_time = strtotime($end_time_str);
		}
		if ($start_time === false || $end_time === false) {
			// Invalid input, handle error
			die("Invalid start or end time");
		}
		$users = $this->userManager->search('');
		$userFilesByTime = [];
		foreach ($users as $user) {
			$uid = $user->getUID();
			$countuploadedfiles=0;
			$countcreatedfiles=0;
			$userFolder = $this->rootFolder->getUserFolder($user->getUID());
			$rootFolderContents = $userFolder->getDirectoryListing();
			$countuploadedfiles = $this->getFilesCountInTimeRange($rootFolderContents,$start_time,$end_time);		
			
			$stmt = $db->prepare('SELECT COUNT(*) as count FROM oc_activity WHERE `user` = ? AND `type` = "file_created" AND `timestamp` BETWEEN ? AND ?');
			$stmt->execute([$uid, $start_time, $end_time]);
			$result = $stmt->fetch();
			$countcreatedfiles = $result['count'];
	
			$userFilesByTime[$uid] = [
                'displayName' => $user->getDisplayName(),
				"createdfilescount" => $countcreatedfiles,
                'uploadedfilescount' => $countuploadedfiles,
            ];
			
		}
		return new TemplateResponse('advanceddashboard','filesbytime',['users'=>$userFilesByTime]);
	}

	
}

