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

		this.ShowLocation = function(locationData)
		{
			this.ShowFlag = 'LOCATION';
			this.ShowData = locationData;
		}

		this.ShowTime = function(locationData)
		{
			this.ShowFlag = 'TIME';
			this.ShowData = locationData;
		}

		this.SaveDetail = function(detailData)
		{
			this.UserMsg = "Saving Data";
			var scope = this;

			var newData = {};
			angular.extend(newData, detailData);
			newData.Proposal = null;
			var res = $http.post('SaveChanges.php', angular.toJson(newData));


			res.success(function(data, status, headers, config)
			{
				scope.UserMsg = "Success";
			});
			res.error(function(data, status, headers, config)
			{
				scope.UserMsg = "Error";
			});

			this.ScanData();

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

						//scope.proposalDetails = response.data.details;
						scope.proposalDetails = [];
						for( var idx=0, len=scope.proposalData.length; idx < len; ++idx)
						{
							if ( 'presentations' in scope.proposalData[idx] )
							{
								for (var j = 0, l = scope.proposalData[idx].presentations.length; j < l; ++j)
								{
									scope.proposalDetails.push(scope.proposalData[idx].presentations[j]);
								}
							}
						}


						scope.ScanData();

						scope.event_year = response.data.event_year;
						scope.event_code = response.data.event_code;
					}
					else
					{
						scope.UserMsg = "Data failed to load:" + response.data.msg;
					}
				});
		}

		this.ScanData = function()
		{
			this.locations = {};
			this.times = {};
			if ( this.proposalData )
			{
				for (var idx = 0, len = this.proposalData.length; idx < len; ++idx)
				{
					if ('presentations' in this.proposalData[idx])
					{
						for (var j = 0, l = this.proposalData[idx].presentations.length; j < l; ++j)
						{
							var details = this.proposalData[idx].presentations[j];

							if (!(details.schedule_location in this.locations))
							{
								this.locations[details.schedule_location] = [];
							}
							this.locations[details.schedule_location].push(details);

							if (!(details.schedule_time in this.times))
							{
								this.times[details.schedule_time] = [];
							}
							this.times[details.schedule_time].push(details);
						}
					}
				}
			}

		}

		this.ShowFlag = 'NONE';
		this.ShowMenu = 'PROGRAM';
		this.Reload();
	});

})(window.angular);
