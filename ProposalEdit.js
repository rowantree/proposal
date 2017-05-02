(function(angular) {
    'use strict';

    var myApp = angular.module('ProposalEditApp',[]);

	myApp.filter('orderByKey', function ()
	{
		return function(items)
		{
			var sorted = [];
			var keys = Object.keys(items).sort();
			for (var i = 0; i < keys.length; i++)
			{
				var k = keys[i];
				sorted[i] = items[k];
				sorted[i].$key = k;
			}
			return sorted;
		};
	});

myApp.filter('orderObjectBy', function() {
	return function(items, field, reverse) {
		var filtered = [];
		angular.forEach(items, function(item) {
			  filtered.push(item);
		});
		filtered.sort(function (a, b) {
			return (a[field] > b[field] ? 1 : -1);
	});
	if(reverse) filtered.reverse();
	return filtered;
	};
});




	myApp.controller('RegController', function ($http)
	{
		this.GetKeys = function(obj)
		{
            var keys = [];
			for(var key in obj)
			{
				keys.push(key);
			}
			return keys.sort();
		}


		this.SaveLocation = function()
		{
			var scope = this;
			var res = $http.post('SaveLocation.php', angular.toJson(this.EditLocation));

			res.success(function(data, status, headers, config)
			{
				scope.UserMsg = "Success";
				scope.EditLocation = {LocationId:null, LocationName:''};
				scope.Reload();
			});
			res.error(function(data, status, headers, config)
			{
				scope.UserMsg = "Error";
			});

			this.ScanData();
		}

		this.SaveEventTime = function()
		{
			var scope = this;
			var res = $http.post('SaveEventTime.php', angular.toJson(this.EditEventTime));

			res.success(function(data, status, headers, config)
			{
				scope.UserMsg = "Success";
				scope.EditEventTime = {EventTimeId:null, EventTimeName:''};
				scope.Reload();
			});
			res.error(function(data, status, headers, config)
			{
				scope.UserMsg = "Error";
			});

			this.ScanData();
		}

		this.ShowProposal = function($index)
		{
			this.ShowData = this.proposalData[$index];
			this.ShowFlag = 'FULL';
			this.UserMsg = "Showing Selected Proposal";
		}

		// User has selected a specific presentation to show
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
					this.LastShowFlag = this.ShowFlag;
					this.ShowFlag = 'DETAIL';
					break;
				}
			}


		}

		this.ShowLocation = function(locationData, scheduleLocation)
		{
			this.ShowFlag = 'LOCATION';
			this.ShowData = locationData;
			this.UserMsg = "Showing Proposals Assigned To " + scheduleLocation;
		}

		this.ShowTime = function(locationData, scheduleTime)
		{
			this.ShowFlag = 'TIME';
			this.ShowData = locationData;
			this.UserMsg = "Showing Proposals Assigned To " + scheduleTime;
		}

		this.ProposalMaint = function(detailData, action)
		{
			this.UserMsg = action;
			var scope = this;
			this.action = 'none';

			var url = 'ProposalMaint.php?proposal_detail_id=' + detailData.proposal_detail_id + "&action=" + action;
			$http.get(url)
				.then(function(response) {
					scope.UserMsg = response.data.msg;
					if (response.data.status === "SUCCESS") {
						scope.Reload();
						scope.ShowFlag = scope.LastShowFlag;
					}
				});
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

						scope.availableLocations = {};
						scope.locationsByName = {};
						scope.availableLocations[null] = {LocationId:null, LocationName:'Unspecified'};
						for (var idx=0; idx < response.data.locations.length; ++idx)
						{
							scope.availableLocations[response.data.locations[idx].LocationId] = response.data.locations[idx];
							scope.locationsByName[response.data.locations[idx].LocationName] = response.data.locations[idx];
						}

						scope.availableTimes = {};
						scope.availableTimes[null] = {EventTimeId:null, EventTimeName:'Unspecified'};
						for (var idx=0; idx < response.data.event_times.length; ++idx)
						{
							scope.availableTimes[response.data.event_times[idx].EventTimeId] = response.data.event_times[idx];
						}

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

							/*
							var LocationName = 'Unspecified';
							for (var k = 0; k < this.availableLocations.length; ++k )
							{
								if ( this.availableLocations[k].LocationId == details.schedule_location )
								{
									locationName = this.availableLocations[k].LocationName;
									break;
								}
							}
							*/

							var locationName = 'Unspecified';
							var locationId = 0;
							if ( details.schedule_location != null )
							{
								locationName = this.availableLocations[details.schedule_location].LocationName;
								locationId = this.availableLocations[details.schedule_location].LocationId;
							}

							if (!(locationName in this.locations))
							{
								this.locations[locationName] = {};
								this.locations[locationName].proposals = [];
								this.locations[locationName].locationId = locationId;
							}
							this.locations[locationName].proposals.push(details);

							var timeName = 'Unspecified';
							var timeId = 0;
							var timeSort = '';

							if ( details.schedule_time != null )
							{
								timeName = this.availableTimes[details.schedule_time].EventTimeName;
								timeId = this.availableTimes[details.schedule_time].EventTimeId;
								timeSort = this.availableTimes[details.schedule_time].EventTimeSort;
							}
							if (!(timeSort in this.times))
							{
								this.times[timeSort] = {};
								this.times[timeSort].proposals = [];
								this.times[timeSort].timeId =timeId;
								this.times[timeSort].name = timeName;
							}
							this.times[timeSort].proposals.push(details);
						}
					}
				}
			}

			// sort times by timeSort

		}

		this.action = 'none';
		this.ShowFlag = 'GRID';
		this.ShowMenu = 'Program';
		this.Reload();


	});

})(window.angular);
