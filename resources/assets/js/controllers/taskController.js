(function () {
    'use strict';

    angular.module('app').controller('TaskController', function ($scope, $http) {

            $scope.todos = [];
            $scope.add = true;
			$scope.error = '';
            $scope.update = false;

            $scope.init = function()
            {
                $http.get('/task').success(function(data)
                {
                    $scope.tasks = data;

                })
                .error(function(data, status) {
                     $scope.messages = data || "Request failed";
                     $scope.status = status;
                });
            };

            $scope.addTask = function()
            {
                $http.post('/task/save', {
                    todo: $scope.todo,
                    done: $scope.done
                }).success(function(data, status, headers, config)
                {
                    $scope.tasks = data;
                    $scope.todo = '';
                    $scope.done = false;
					$scope.error = '';
                })
                .error(function(data)
                {
                	$scope.error = data.message;
                });
            };

            $scope.editTask = function(index)
            {
                $scope.add = false;
                $scope.update = true;
                var task = $scope.tasks[index];
				$scope.error = '';

                $scope.todo = task.todo;
                $scope.done = task.done;
                $scope.id = task.id;
            };

            $scope.updateStatus = function(id)
            {

                $http.patch('/task/' + id + '/status', {
                    done: $scope.done
                })
				.success(function(data, status, headers, config)
                {
                    $scope.tasks = data;
                    $scope.add = true;
                    $scope.update = false;
					$scope.error = '';
                })
				.error(function(data){
					$scope.error = data.message;
				});
            };

            $scope.updateTask = function()
            {

                $http.patch('/task/' + $scope.id + '/update', {
                    todo: $scope.todo,
					done: $scope.done
                }).success(function(data, status, headers, config)
                {
                    $scope.tasks = data;
                    $scope.id = 0;
                    $scope.todo = '';
                    $scope.done = false;
                    $scope.add = true;
                    $scope.update = false;
					$scope.error = '';
                })
                .error(function(data)
                {
                	$scope.todo = '';
                	$scope.done = false;
                    $scope.error = data.message;
                });
            };

            $scope.deleteTask = function(id)
            {

                if(confirm('Are you sure you want to delete this task?'))
                {
                    $http.delete('/task/' + id + '/delete')
                        .success(function(data)
                        {
                            $scope.tasks = data;

                        })
						.error(function(data)
						{
							$scope.error = data.message;
						});
                }
            };

            $scope.remaining = function()
            {
                var count = 0;
                angular.forEach($scope.tasks, function(task)
                {
                    count += task.done ? 0 : 1;
                });
                return count;
            };

            $scope.init();

    });
})();