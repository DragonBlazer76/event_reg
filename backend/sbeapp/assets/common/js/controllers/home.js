SAPP.controller('home', ["$scope", "authServices", "commonServices", function($scope, authServices, commonServices) {
        $scope.companyName = "";
        $scope.email = "";
        $scope.password = "";
        $scope.agreeTC = "";
        $scope.industryId = 0;
        $scope.contactPerson = "";
        $scope.fname = "";
        $scope.lname = "";
        $scope.errorCode = '';
        
        init();

        function init() {
        };
        
        $scope.checkEmail = function( checkDB ) {
            console.log("checkEmail >> "+checkDB);
            if (typeof $scope.email === "undefined" || $scope.email === "") {
                return $scope.errorCode = "EMAIL_IS_EMPTY";
            }
            console.log( $scope.email+ " "+isValidEmail($scope.email) );
            if(isValidEmail($scope.email) === false){ 
                return $scope.errorCode = "EMAIL_IS_NOT_VALID";
            }
            
            var checkDB = typeof checkDB!=="undefined" ? checkDB : false;
            if( checkDB===false ){
                authServices.checkEmailExists($scope.email).then(function(response) {
                    $scope.errorCode = typeof response.errorCode!=="undefined" ? response.errorCode : "";
                    console.log($scope.errorCode);
                });
            } else {
                $scope.errorCode = "";
            }
        }; //end checkEmail function 

        $scope.formValidate = function($event) {
            var isValid = true;
            //form validation
            if (!$scope.frmRegister.$valid) {
                console.log("Complete all fields.");
                isValid = false;
            }
            //check whether agree to Tc's is checked
//            if( $scope.agreeTC !== true ){
//                $scope.errorCode = "AGREE_TO_TC";
//                isValid = false;
//            }

            if ( $scope.errorCode!=="" ) {
                console.log($scope.errorCode);
                isValid = false;
            }
            
            if (isValid === false) {
                $event.preventDefault();
            } else {
                $scope.submitted = true;
            }
        }; //end formValidate function 

        $scope.loginFormValidate = function($event) {
            var isValid = true;
            //form validation
            if (!$scope.frmLogin.$valid) {
                console.log("Complete all fields.");
                isValid = false;
            }

            if ( $scope.errorCode!=="" ) {
                console.log($scope.errorCode);
                isValid = false;
            }
            
            if (isValid === false) {
                $event.preventDefault();
            } else {
                $scope.submitted = true;
            }
        }; //end formValidate function 

    }]);
