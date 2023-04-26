(function ($, OC) {
    const objectMap = (obj, fn) =>
    Object.fromEntries(
      Object.entries(obj).map(
        ([k, v], i) => [k, fn(v, k, i)]
      )
    )

	$(document).ready(function () {
		const ctx = document.getElementById('files-count');
        data = $('#files-count').data('files-count');

        $('#dataTable').DataTable({
		footerCallback: function (row, data, start, end, display) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
 
            // Total over all pages
            totalFilesCreated = api
                .column(1)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
 
            // Total over this page
            totalFilesUploaded = api
                .column(2)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
 
            // Update footer
            $(api.column(1).footer()).html(totalFilesCreated);
            $(api.column(2).footer()).html(totalFilesUploaded);
        },
	});

        filesCountChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: getFilesCountLabels(data),
					datasets: [{
						label: " ",
						data: getFilesCounts(data),
						backgroundColor: [
							'rgba(0, 76, 153, 0.2)',
							'rgba(51, 153, 255, 0.2)',
							'rgba(207, 102, 0, 0.2)',
							'rgba(255, 178, 102, 0.2)',
							'rgba(0, 153, 0, 0.2)',
							'rgba(153, 255, 51, 0.2)',
							'rgba(178, 102, 255, 0.2)'
						],
						borderColor: [
							'rgba(0, 76, 153, 1)',
							'rgba(51, 153, 255, 1)',
							'rgba(207, 102, 0, 1)',
							'rgba(255, 178, 102, 1)',
							'rgba(0, 153, 0, 1)',
							'rgba(153, 255, 51, 1)',
							'rgba(178, 102, 255, 1)'
						],
						borderWidth: 1
					}]
				},
				options: {
					plugins: {
						legend: { display: false },
						tooltip: {
                                                        callbacks: {
                                                          label:  function(tooltipItem) {
                                                                          const value = filesCountChart.data.datasets[0].data[tooltipItem.dataIndex];
                                                                          return `${value}: Files`;
                                                                        }
                                                          }
                                                  }
					},
					scales: {
						yAxes: {
							ticks: {
								min: 0,
								stepSize: 1,
								callback:(value)=>{
                                                                        return value + " Files"
                                                                }
							}
						}
					}
				}
			});

		filesCountChart.update();


    //-----------------used Space Chart------------------------
        const usedSpace = document.getElementById('used-space');
        var usedSpaceData = $('#used-space').data('used-space');


        var usedSpaceChart = new Chart(usedSpace, {
				type: 'bar',
				data: {
					labels: getSpaceUsedLabels(usedSpaceData),
					datasets: [{
						label: "",
						data: getSpaceUsed(usedSpaceData),
						backgroundColor: [
							'rgba(207, 102, 0, 0.2)',
							'rgba(0, 76, 153, 0.2)',
							'rgba(51, 153, 255, 0.2)',
							'rgba(255, 178, 102, 0.2)',
							'rgba(0, 153, 0, 0.2)',
							'rgba(153, 255, 51, 0.2)',
							'rgba(178, 102, 255, 0.2)'
						],
						borderColor: [
							'rgba(207, 102, 0, 1)',
							'rgba(0, 76, 153, 1)',
							'rgba(51, 153, 255, 1)',
							'rgba(255, 178, 102, 1)',
							'rgba(0, 153, 0, 1)',
							'rgba(153, 255, 51, 1)',
							'rgba(178, 102, 255, 1)'
						],
						borderWidth: 1
					}]
				},
				options: {
					plugins: { 
						legend: { display: false },
						tooltip: {
                                                	callbacks: {
                                                        	label:  function(tooltipItem) {
                                                                                const value = usedSpaceChart.data.datasets[0].data[tooltipItem.dataIndex];
                                                                                return `${value.toFixed(2)}: MB`;
                                                                        }
                                                	}
                                        	}
					},
					scales: {
						yAxes: {
							ticks: {
								min: 0,
								stepSize: 1,
								callback:(value)=>{
                                                                        return value + " MB"
                                                                }
							}
						}
					}
				}
			});

            usedSpaceChart.update();

	});


	function getThemedPrimaryColor() {
		return OCA.Theming ? OCA.Theming.color : 'rgb(54, 129, 195)';
	}

	function getThemedPassiveColor() {
		return OCA.Theming && OCA.Theming.inverted ? 'rgb(55, 55, 55)' : 'rgb(200, 200, 200)';
	}

    function getFilesCountLabels(data) {
        mappedObject = objectMap(data, v => v.displayName);

        let arr = [];
        for (let key in mappedObject)
            arr.push(mappedObject[key]);

        return arr;
    }

    function getFilesCounts(data) {
        mappedObject = objectMap(data, v => v.filecount);

        let arr = [];
        for (let key in mappedObject)
            arr.push(mappedObject[key]);

        return arr;
    }

    function getSpaceUsedLabels(data) {
        mappedObject = objectMap(data, v => v.displayName);

        let arr = [];
        for (let key in mappedObject)
            arr.push(mappedObject[key]);

        return arr;
    }

    function getSpaceUsed(data) {
        mappedObject = objectMap(data, v => v.usedSpace);

        let arr = [];
        for (let key in mappedObject)
            arr.push(mappedObject[key]/1000000.0);

        return arr;
    }

})(jQuery, OC);
