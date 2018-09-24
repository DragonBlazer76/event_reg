var delEventId = 0;

function confirmDeleteEvent(name, total, id){
    $("#modDeleteEventName").html(name);
    $("#modDeleteEventGuestsTotal").html(total);
    $('#modDeleteEvent').modal()
    delEventId = id;
}

SAPP.controller('events', ["$scope", "commonServices", function($scope, commonServices) {
        $scope.getListsUrl = gAPP.siteUrl + "events/getEventLists";
        $scope.objTable = "";
        $scope.evntStatus = "published";

        init();

        function init() {

            if ($('#tblEventsList').length) {
                $("#btnLinkNewGuest").bind("mousedown", function(e) {
                    $(this).attr("href", gAPP.siteUrl + "guests/new?event_id=" + $scope.eventId);
//                    e.preventDefault();
//                    e.stopPropagation();
                });

                loadGuests();
            }

            $("#modDeleteEventConfDelete").bind("click", function(e){
                redirect(gAPP.siteUrl+"events/deleteEvent?id="+delEventId, true);
            });
            
//            if( $('#frmGuest').length ){
//                $scope.guestStatus = $('[name=status] option:selected').val();
//                doGuestStatus();
//            }


        }
        ;

        function loadGuests() {

            commonServices.getEventLists($scope.getListsUrl, {}).then(function(response) {
                  if (jQuery().dataTable) {
                    $scope.objTable = $('#tblEventsList').dataTable({
                        "data": response.lists,
                        "aoColumns": [
                            {"data": "edit_link", "bSortable ": null},
                            {"data": "delete_link", "bSortable ": null},
                            {"data": "name", "sSortDataType": "dom-text"},
                            {"data": "guests_total", "sSortDataType": "dom-text"},
                            {"data": "start_date_f", "sSortDataType": "dom-text"},
                            {"data": "end_date_f", "sSortDataType": "dom-text"},
//                            {"data": "status", "sSortDataType": "dom-text"},
                            {"data": "created_date_f", "sSortDataType": "dom-text"},
                        ]
                    });
                }
            });
        }
        ; //end loadGuests function 

        function doGuestStatus() {
            if ($scope.guestStatus == "verified") {
                $(".grpVerifiedFields").show().find(".form-control").attr("required");
            } else {
                $(".grpVerifiedFields").hide().find(".form-control").removeAttr("required");
            }
        }

        $scope.setEvent = function() {
//           $scope.objTable.fnClearTable();
            $scope.objTable.fnDestroy();
            loadGuests();
        };

        $scope.checkGuestStatus = function() {
            doGuestStatus();
        };


    }]);