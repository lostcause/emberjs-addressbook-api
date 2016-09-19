(function()
{
	'use strict';

	var app = angular.module('app', [
		'ngStorage',
		'ngRoute',
		'ngAnimate',
		'angular-loading-bar'
	]);

	app.constant('urls', {
		BASE: 'http://laravel-todo-list.dev'
	})
		.config(['$routeProvider',
				 '$httpProvider',
				 function($routeProvider, $httpProvider)
				 {
					 $routeProvider.when('/', {
						 templateUrl: 'partials/tasks.html',
						 controller : 'TaskController'
					 }).when('/signin', {
						 templateUrl: 'partials/signin.html',
						 controller : 'AuthController'
					 }).when('/signup', {
						 templateUrl: 'partials/signup.html',
						 controller : 'AuthController'
					 }).when('/tasks', {
						 templateUrl: 'partials/tasks.html',
						 controller : 'TaskController'
					 }).when('/save', {
						 controller: 'TaskController'
					 }).otherwise({
						 redirectTo: '/'
					 });

					 $httpProvider.interceptors.push(['$q',
													  '$location',
													  '$localStorage',
													  function($q, $location, $localStorage)
													  {
														  return {
															  'request'      : function(config)
															  {
																  config.headers = config.headers || {};
																  if($localStorage.token)
																  {
																	  config.headers.Authorization = 'Bearer ' + $localStorage.token;
																  }
																  return config;
															  },
															  'responseError': function(response)
															  {
																  if(response.status === 401 || response.status === 403)
																  {
																	  delete $localStorage.token;
																	  $location.path('/signin');
																  }
																  return $q.reject(response);
															  }
														  };
													  }]);

				 }
		]).run(function($rootScope, $location, $localStorage)
	{
		$rootScope.$on("$routeChangeStart", function(event, next)
		{
			if($localStorage.token == null)
			{
				if(next.templateUrl === "partials/tasks.html")
				{
					$location.path("/signin");
				}
			}
		});

	});
})();