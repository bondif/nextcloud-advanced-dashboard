<?php
namespace OCA\AdvancedDashboard\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class Admin implements ISettings {

    public function getSection() {
        return 'advanceddashboard';
    }

    public function getPriority() {
        return 100;
    }

    public function getIconName() {
        return 'app.svg';
    }

    public function getDisplayName() {
        return 'Advanced Dashboard';
    }

    public function getSettings() {
        $template = new TemplateResponse('advanceddashboard', 'admin');
        $template->assign('advanceddashboard', 'advanceddashboard');
        return $template;
    }

    public function handleSave($post) {
        // Implement this method if you need to handle form submissions
    }
}
