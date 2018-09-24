var delGuestId = 0;

function confirmDeleteGuest(name, id) {
    $("#modDeleteGuestName").html(name);
    $('#modDeleteGuest').modal()
    delGuestId = id;
}

SAPP.controller('guests', ["$scope", "commonServices", function ($scope, commonServices) {

        $scope.getListsUrl = gAPP.siteUrl + "web/getLists";
        $scope.eventId = "";
        $scope.objTable = "";
        $scope.guestStatus = "registered";

        init();

        function init() {
            var uParams = getUrlParams();

            if ($('#tblGuestsList').length) {
                loadGuests();
            }

            if (typeof uParams.eid !== "undefined" && uParams.eid !== "") {
                $scope.eventId = uParams.eid;
                getEvent();
            }

            if ($('#frmGuest').length) {
                $scope.guestStatus = $('[name=status] option:selected').val();
                doGuestStatus();
            }

            $("#modDeleteGuestConfDelete").bind("click", function (e) {
                redirect(gAPP.siteUrl + "guests/deleteGuest?eid=" + $scope.eventId + "&id=" + delGuestId, true);
            });

            shCTADiv($scope.eventId == "" ? false : true);

        }
        ;

        function shCTADiv(isShow) {
            var isShow = typeof isShow !== "undefined" ? isShow : false;
            if (isShow === false) {
                $("#divCTAButtons").hide();
            } else {
                $("#divCTAButtons").show();
            }
        }
        
        function loadGuestsSummary(){
            if ($scope.eventId == 0 || $scope.eventId == "") {
                return;
            }
            
            commonServices.getLists(gAPP.siteUrl + "web/getListSummary", {eventId: $scope.eventId, type: 'g'}).then(function (response) {
                $("#evtTotalGuests").text( response.results.total );
                $("#evtTotalGuestsReg").text( response.results.registered );
                $("#evtTotalGuestsUNReg").text( response.results.unregistered );
                $("#evtTotalGuestsLogout").text( response.results.logout );
            });

        }
        
        function loadGuests() {
            if ($scope.eventId == 0 || $scope.eventId == "") {
                return;
            }

            commonServices.doPreloader('mListGuests', true);

            commonServices.getLists($scope.getListsUrl, {eventId: $scope.eventId, type: 'g'}).then(function (response) {

                if (jQuery().dataTable) {
                    $scope.objTable = $('#tblGuestsList').dataTable({
                        "data": response.lists,
                        "aoColumns": [
                            {"data": "edit_link", "bSortable ": null},
                            {"data": "delete_link", "bSortable ": null},
                            {"data": "code", "sSortDataType": "dom-text"},
                            {"data": "nric", "sSortDataType": "dom-text"},
                            {"data": "fname", "sSortDataType": "dom-text"},
                            {"data": "lname", "sSortDataType": "dom-text"},
                            {"data": "email", "sSortDataType": "dom-text"},
                            {"data": "status", "sSortDataType": "dom-text"},
                            {"data": "tableno", "sSortDataType": "dom-text"},
                            {"data": "registered_date_f", "sSortDataType": "dom-text"}
//                            {"data": "created_date_f", "sSortDataType": "dom-text"}
                        ]
                    });
                    commonServices.doPreloader('mListGuests', false);
                }
            });
        }
        ; //end loadGuests function 

        function doGuestStatus() {
            if ($scope.guestStatus == "registered") {
                $(".grpVerifiedFields").show().find(".form-control").attr("required", "required");
            } else {
                $(".grpVerifiedFields").hide().find(".form-control").removeAttr("required");
            }
        }

        $scope.updateEventId = function () {
            shCTADiv($scope.eventId == "" ? false : true);
            $("#btnLinkNewGuest").attr("href", gAPP.siteUrl + "guests/new?event_id=" + $scope.eventId);
            $("#btnLinkImportGuest").attr("href", gAPP.siteUrl + "guests/import?event_id=" + $scope.eventId);
        };

        function getEvent() {
            if ($scope.eventId == "" || $scope.eventId == 0) {
                alert("Please select an event...");
                return;
            }

            if ($.fn.DataTable.isDataTable('#tblGuestsList')) {
                $scope.objTable.fnClearTable();
                $scope.objTable.fnDestroy();
            }

            loadGuests();
            loadGuestsSummary();
        }

        $scope.setEvent = function () {
            getEvent();
        };

        $scope.checkGuestStatus = function () {
            doGuestStatus();
        };

        $scope.csvDownload = function () {
            var url = "web/generatereport?type=csv&method=" + $("#fileter-val option:selected").val() + "&event_id=" + $scope.eventId;
            $("#csvDownload").attr('href', url);
        };

        $scope.pdfDownload = function () {
            var url = "web/pdfreport?event_id=" + $scope.eventId;
            $("#pdfDownload").attr('href', url);
        };

    }]);