(function(angular) {
    'use strict';

    var myApp = angular.module('ProposalEditApp',[]);

    myApp.controller('RegController', function ($http)
    {
		this.ShowProposal = function($index)
		{
			this.ShowData = this.proposalData[$index];
			this.ShowFlag = 'FULL';
		}

		this.ShowDetail = function(proposal_detail_id)
		{
			for( var idx=0, len=this.proposalDetails.length; idx < len; ++idx )
			{
				if ( this.proposalDetails[idx].proposal_detail_id == proposal_detail_id)
				{
					this.ShowData = this.proposalDetails[idx];
					for( var idx2=0, len2=this.proposalData.length; idx2 < len2; ++idx2 )
					{
						if ( this.proposalData[idx2].proposal_id == this.ShowData.proposal_id )
						{
							this.ShowData.Proposal = this.proposalData[idx2];
						}
					}
					this.ShowFlag = 'DETAIL';
					break;
				}
			}

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
						scope.proposalDetails = response.data.details;
						scope.event_year = response.data.event_year;
						scope.event_code = response.data.event_code;
					}
					else
					{
						scope.UserMsg = "Data failed to load:" + response.data.msg;
					}
				});
		}

		this.ShowFlag = 'NONE';
		this.ShowMenu = 'PEOPLE';
		this.Reload();
	});

})(window.angular);
