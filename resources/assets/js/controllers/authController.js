(function () {
	'use strict';

	var app = angular.module('app');

	app.controller('AuthController', ['$rootScope', '$scope', '$location', '$localStorage', 'Auth',
		   function ($rootScope, $scope, $location, $localStorage, Auth) {
			   function successAuth(res) {
				   $localStorage.token = res.token;
				   window.location = "/";
			   }

			   $scope.signin = function () {
				   var formData = {
					   email: $scope.email,
					   password: $scope.password
				   };

				   Auth.signin(formData, successAuth, function () {
					   $rootScope.error = 'Invalid credentials.';
				   })
			   };

			   $scope.signup = function () {
				   var formData = {
					   email: $scope.email,
					   password: $scope.password
				   };

				   Auth.signup(formData, successAuth, function (res) {
					   $rootScope.error = res.error || 'Failed to sign up.';
				   })
			   };

			   $scope.logout = function () {
				   Auth.logout(function () {
					   window.location = "/"
				   });
			   };

			   $scope.getClass = function (path) {
				   return ($location.path().substr(0, path.length) === path) ? 'active' : '';
			   };

			   $scope.token = $localStorage.token;
		   }]);
})();
