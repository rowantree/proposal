(function(angular) {
    'use strict';

    var myApp = angular.module('ProposalEditApp',[]);

    myApp.component('greetUser', 
		{
		    template: 'Hello, {{$ctrl.user}}!',
		    controller: function GreetUserController() 
		    {
		        this.user = 'world';
		    }
		}
		);

    myApp.controller('RegController', function ($http)
    {
		this.ShowProposal = function($index)
		{
			this.ShowData = this.proposalData[$index];
		}


		this.Reload = function()
		{
			this.UserMsg = "Loading data from server";
			this.clanData = [];
			this.changeCnt = 0;
			this.userMsg = "Starting Reload";
			var scope = this;
			$http.get("GetProposalData.php")
				.then(function(response)
				{
					if ( response.data.status == 'SUCCESS' )
					{
						scope.UserMsg = "Data has been loaded: " + response.data.status;
						scope.proposalData = response.data.proposals;
					}
					else
					{
						scope.UserMsg = "Data failed to load:" + response.data.msg;
					}
				});
		}

		this.Reload();
	});

})(window.angular);
