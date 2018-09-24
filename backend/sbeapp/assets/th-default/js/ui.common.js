$(document).ready(function() {
    if (jQuery().daterangepicker) {
        $('.dateRangePicker').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD hh:mm:ss'});
    }
//    if (jQuery().dataTable) {
//        $('#tblGuestsList').dataTable({
////            "ajax": gAPP.siteUrl + "web/getLists?type=guest&id=4",
////            "processing": true,
////            "serverSide": true,
//            "data": [
//                {
//                    "name": "Tiger Nixon",
//                    "position": "System Architect",
//                    "salary": "$3,120",
//                    "start_date": "2011/04/25",
//                    "office": "Edinburgh",
//                    "extn": 5421
//                },
//                {
//                    "name": "Garrett Winters",
//                    "position": "Director",
//                    "salary": "5300",
//                    "start_date": "2011/07/25",
//                    "office": "Edinburgh",
//                    "extn": "8422"
//                },
//            ],
//            "columns": [
//                {"data": "name"},
//                {"data": "position"},
//                {"data": "office"},
//                {"data": "extn"},
//                {"data": "start_date"},
//                {"data": "salary"}
//            ]
//        });
//    }
});