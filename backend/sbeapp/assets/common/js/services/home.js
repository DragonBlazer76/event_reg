SAPP.service("authServices",
        function($http, $q) {

            return({
                checkEmailExists: checkEmailExists
            });

            function checkEmailExists(email) {
                
                if (typeof email === "undefined" || email === "") {
                    return false;
                }
                
                if( isValidEmail(email)===false ){
                    return false;
                }
                
                var params = $.param({ email:email });
                
                var request = $http({
                    method: "post",
                    url: gAPP.siteUrl + "reqs/checkEmail",
                    headers: gAPP.formHeaders,
                    data : params
                });
                return(request.then(handleSuccess, handleError));
            }

            function handleError(response) {
                if (!angular.isObject(response.data) || !response.data.message) {
                    return($q.reject("An unknown error occurred."));
                }
                return($q.reject(response.data.message));
            }

            function handleSuccess(response) {
                return(response.data);
            }
        });