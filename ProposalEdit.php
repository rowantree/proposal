<html><head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>Proposal Editor</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.angularjs.org/1.5.7/angular.js"></script>
    <script type="text/javascript" src="ProposalEdit.js"></script>

    <style>
        .scrollbar {
            height: auto;
            max-height: 90vh;
            overflow-x: hidden;
        }
    </style>

</head>
<body style="margin-right:50px">
	<div ng-app="ProposalEditApp">
		<div ng-controller="RegController as reg">

            <div class="row">

                <!--
                    Left Hand Menu
                -->
                <div class="col-md-2 scrollable-menu scrollbar" role="menu">
                    <button class="btn-block btn-primary" ng-click="reg.ShowFlag='GRID'">Show Grid</button>
                    <button class="btn-block btn-primary" ng-click="reg.ShowMenu='People'">Show People</button>
                    <button class="btn-block btn-primary" ng-click="reg.ShowMenu='Program'">Show Program</button>
                    <button class="btn-block btn-primary" ng-click="reg.ShowMenu='Location'">Show Location</button>
                    <button class="btn-block btn-primary" ng-click="reg.ShowMenu='Time'">Show Time</button>

                    <div class="text-center bg-success"> {{reg.ShowMenu}} </div>

                    <div ng-show="reg.ShowMenu=='People'" ng-repeat="data in reg.proposalData" style="padding: 2px 2px 2px 2px;">
                        <button class="btn-block text-left btn-link" ng-click="reg.ShowProposal($index)">{{data.legal_name}}</button>
                    </div>

                    <div ng-show="reg.ShowMenu=='Program'">
                        Sort by:
                        <button class="btn-info" ng-click="sortType='proposal_detail_id'">Id</button>
                        <button class="btn-info" ng-click="sortType='title'">Name</button>
                        <div ng-repeat="data in reg.proposalDetails | orderBy: sortType" style="padding: 2px 2px 2px 2px;">
                            <button class="btn-block text-left btn-link" ng-click="reg.ShowDetail(data.proposal_detail_id)">[{{('000'+data.proposal_detail_id).slice(-3)}}] {{data.title}}</button>
                        </div>
                    </div>

                    <div ng-show="reg.ShowMenu=='Location'" ng-repeat="location in reg.GetKeys(reg.locations)" style="padding: 2px 2px 2px 2px;">
                        <button class="btn-block text-left btn-link" ng-click="reg.ShowLocation(reg.locations[location],location)">{{location}}</button>
                    </div>

                    <div ng-show="reg.ShowMenu=='Time'" ng-repeat="index in reg.GetKeys(reg.times)" style="padding: 2px 2px 2px 2px;">
                        <button class="btn-block text-left btn-link" ng-click="reg.ShowTime(reg.times[index],index)">{{reg.times[index].name}}</button>
                    </div>

                    <button class="btn-block btn-info" ng-click="reg.ShowFlag='EDIT_LOCATIONS'">Edit Locations</button>
                    <button class="btn-block btn-info" ng-click="reg.ShowFlag='EDIT_TIMES'">Edit Times</button>

                </div>

                <!--
                    Right Hand Data Panel
                -->

                <div class="col-md-10">

                    <div class="row">
                        <div class="alert-success">Event:{{reg.event_code}} {{reg.event_year}} {{reg.UserMsg}}    [{{reg.ShowFlag}}]</div>
                    </div>

                    <div ng-show="reg.ShowFlag=='GRID'">
                        <table border="1">
                            <tr>
                                <td></td>
                                <td ng-repeat="location in reg.GetKeys(reg.locations)" style="padding: 2px 2px 2px 2px;">
                                    {{location}}
                                </td>
                            </tr>
                            <tr ng-repeat="timeSort in reg.GetKeys(reg.times)" style="padding: 2px 2px 2px 2px;">
                                <td>{{reg.times[timeSort].name}}</td>
                                <td ng-repeat="location in reg.GetKeys(reg.locations)">
                                    <span ng-repeat="propDetails in reg.times[timeSort].proposals | filter : { schedule_location : reg.locations[location].locationId } ">
                                        #{{propDetails.proposal_detail_id}} {{propDetails.title}}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <!--
                        Edit Locations
                    -->
                    <div ng-show="reg.ShowFlag=='EDIT_LOCATIONS'">
                        <h2>Location Editor</h2>
                        <button class="btn-info" ng-click="reg.EditLocation={LocationId:0, LocationName:'New'}">New</button>
                        <div ng-repeat="data in reg.locationsByName">
                            <div class="row" ng-if="data.LocationId!=null">
                                <button ng-click="reg.EditLocation = {LocationId:data.LocationId, LocationName:data.LocationName}">Edit</button>
                                {{data.LocationName}}
                            </div>
                        </div>
                        <div ng-show="reg.EditLocation.LocationId!=null">
                        #{{reg.EditLocation.LocationId}}<Input Type="text" Name="LocationName" ng-model="reg.EditLocation.LocationName"><button ng-click="reg.SaveLocation()">Save</button>
                        </div>
                    </div>

                    <!--
                        Edit Times
                    -->
                    <div ng-show="reg.ShowFlag=='EDIT_TIMES'">
                        <h2>Times Editor</h2>
                        <button class="btn-info" ng-click="reg.EditEventTime={EventTimeId:0, EventTimeName:'New'}">New</button>

                        <div ng-repeat="item in reg.availableTimes | orderObjectBy:'EventTimeSort':false ">
                            <div class="row" ng-if="item.EventTimeId!=null">
                                <button ng-click="reg.EditEventTime = {
                                    EventTimeId:item.EventTimeId,
                                    EventTimeName:item.EventTimeName,
                                    EventTimeSort:item.EventTimeSort
                                }">Edit</button>
                                {{item.EventTimeName}}
                                [{{item.EventTimeSort}}]
                            </div>
                        </div>

                        <div ng-show="reg.EditEventTime.EventTimeId!=null">
                            #{{reg.EditEventTime.EventTimeId}}
                            <Input Type="text" Name="EventTimeName" ng-model="reg.EditEventTime.EventTimeName">
                            <Input Type="text" Name="EventTimeSort" ng-model="reg.EditEventTime.EventTimeSort">
                            <button ng-click="reg.SaveEventTime()">Save</button>
                        </div>

                    </div>


                    <!--
                        List Locations
                    -->
                    <div ng-show="reg.ShowFlag=='LOCATION'" ng-repeat="data in reg.ShowData.proposals">
                        <!-- Each data element is a presentation detail -->
                        <div class="row">
                            <div class="col-md-2">{{reg.availableTimes[data.schedule_time].EventTimeName}}</div>
                            <div class="col-md-10"><button class="btn-block text-left" ng-click="reg.ShowDetail(data.proposal_detail_id)">[{{('000'+data.proposal_detail_id).slice(-3)}}] {{data.title}}</button></div>
                        </div>
                    </div>

                    <!--
                        List Time
                    -->
                    <div ng-show="reg.ShowFlag=='TIME'" ng-repeat="data in reg.ShowData.proposals">
                        <!-- Each data element is a presentation detail -->
                        <div class="row">
                            <div class="col-md-2">{{reg.availableLocations[data.schedule_location].LocationName}}</div>
                            <div class="col-md-10"><button class="btn-block text-left" ng-click="reg.ShowDetail(data.proposal_detail_id)">[{{('000'+data.proposal_detail_id).slice(-3)}}] {{data.title}}</button></div>
                        </div>
                    </div>


                    <!--
                        Details of selected presentation
                    -->
                    <div ng-show="reg.ShowFlag=='DETAIL'">
                        <div class="row">
                            <div class="col-md-2"><b>Title</b></div>
                            <div class="col-md-10"><input class="form-control" type="text" ng-model="reg.ShowData.title"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Location</div>
                            <div class="col-md-10">
                                <select class="form-control" ng-model="reg.ShowData.schedule_location" ng-options="item.LocationId as item.LocationName for item in reg.locationsByName"></select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Time</div>
                            <div class="col-md-10">
                                <select class="form-control" ng-model="reg.ShowData.schedule_time" ng-options="item.EventTimeId as item.EventTimeName for item in reg.availableTimes | orderObjectBy: 'EventTimeSort':false"></select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Description</div>
                            <div class="col-md-10"><textarea class="form-control" ng-model="reg.ShowData.presentation"></textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Id</div>
                            <div class="col-md-10">#{{reg.ShowData.proposal_detail_id}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Type</div>
                            <div class="col-md-10">{{reg.ShowData.presentation_type}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Space</div>
                            <div class="col-md-8">{{reg.ShowData.space_preference}}</div>
                            <div class="col-md-2">{{reg.ShowData.space_preference_other}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Audience</div>
                            <div class="col-md-10">{{reg.ShowData.target_audience}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Time Pref.</div>
                            <div class="col-md-10">{{reg.ShowData.time_preference}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Age</div>
                            <div class="col-md-8">{{reg.ShowData.age}}</div>
                            <div class="col-md-2">{{reg.ShowData.age_other}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Fee</div>
                            <div class="col-md-8">{{reg.ShowData.fee}}</div>
                            <div class="col-md-2">{{reg.ShowData.fee_detail}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Limit</div>
                            <div class="col-md-8">{{reg.ShowData.limit}}</div>
                            <div class="col-md-2">{{reg.ShowData.limit_detail}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Equipment</div>
                            <div class="col-md-10">{{reg.ShowData.equipment}}</div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div ng-show="reg.action=='none'">
									<button ng-click="reg.SaveDetail(reg.ShowData)">Save</button>
									<button ng-click="reg.action='DELETE'">Delete</button>
									<button ng-click="reg.action='DUPLICATE'">Duplicate</button>
								</div>
                            </div>
                            <div ng-show="reg.action!='none'" class="col-md-6">
                            <button ng-click="reg.action='none'">Cancel</button>
                            <button ng-click="reg.ProposalMaint(reg.ShowData, reg.action)">Confirm</button>
                            {{reg.action}}
                            </div>
                        </div>


                        <div class="row"><div class="col-md-12"><hr></div></div>

                        <div class="row">
                            <div class="col-md-2">Legal Name</div>
                            <div class="col-md-10">{{reg.ShowData.Proposal.legal_name}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Program Name</div>
                            <div class="col-md-10">{{reg.ShowData.Proposal.program_name}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Email</div>
                            <div class="col-md-10">{{reg.ShowData.Proposal.email_address}}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Phone</div>
                            <div class="col-md-10">{{reg.ShowData.Proposal.telephone_number}}</div>
                        </div>


                        <div class="row">
                            <div class="col-md-2">Availability</div>
                            <div class="col-md-10">
                                <div Style="display: inline;" ng-show="reg.ShowData.Proposal.AvailFri3==1">Friday After 3&nbsp;</div>
                                <div Style="display: inline;" ng-show="reg.ShowData.Proposal.AvailFri8==1">Friday After 8&nbsp;</div>
                                <div Style="display: inline;" ng-show="reg.ShowData.Proposal.AvailSat==1">Saturday&nbsp;</div>
                                <div Style="display: inline;" ng-show="reg.ShowData.Proposal.AvailSun==1">Sunday&nbsp;</div>
                                {{reg.ShowData.Proposal.available}}
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-2">Entry Date</div>
                            <div class="col-md-10">{{reg.ShowData.Proposal.entry_date}}</div>
                        </div>


                    </div>

                    <div ng-show="reg.ShowFlag=='FULL'">

                        <div class="row">
                            <div class="col-md-2">Legal Name</div>
                            <div class="col-md-10">{{reg.ShowData.legal_name}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Program Name</div>
                            <div class="col-md-10">{{reg.ShowData.program_name}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Email</div>
                            <div class="col-md-10">{{reg.ShowData.email_address}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Phone</div>
                            <div class="col-md-10">{{reg.ShowData.telephone_number}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Unavailable</div>
                            <div class="col-md-10">{{reg.ShowData.unavailable_times}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Biography</div>
                            <div class="col-md-10"><textarea class="form-control" ng-model="reg.ShowData.biography"></textarea></div>
                        </div>
                        <div ng-repeat="data in reg.ShowData.otherPeople">
                            <div class="row"><div class="col-md-12"><hr></div></div>
                            <div class="row">
                            <div class="col-md-2"><b>Legal Name</b></div>
                            <div class="col-md-10">{{data.legal_name}}</div>
                            </div>
                            <div class="row">
                            <div class="col-md-2">Program Name</div>
                            <div class="col-md-10">{{data.program_name}}</div>
                            </div>
                            <div class="row">
                            <div class="col-md-2">Biography</div>
                            <div class="col-md-10"><textarea class="form-control" ng-model="data.biography"></textarea></div>
                            </div>
                        </div>

                        <div ng-repeat="data in reg.ShowData.presentations">

                            <div class="row"><div class="col-md-12"><hr></div></div>

                            <div class="row">
                                <div class="col-md-2"><b>Title</b></div>
                                <div class="col-md-10"><button class="btn-block text-left" ng-click="reg.ShowDetail(data.proposal_detail_id)">{{data.title}}</button></div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">Location</div>
                                <div class="col-md-10">{{reg.availableLocations[data.schedule_location].LocationName}}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">Time</div>
                                <div class="col-md-10">{{reg.availableTimes[data.schedule_time].EventTimeName}}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">Description</div>
                                <div class="col-md-10">{{data.presentation}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Type</div>
                                <div class="col-md-10">{{data.presentation_type}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Space</div>
                                <div class="col-md-8">{{data.space_preference}}</div>
                                <div class="col-md-2">{{data.space_preference_other}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Audience</div>
                                <div class="col-md-10">{{data.target_audience}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Time Pref.</div>
                                <div class="col-md-10">{{data.time_preference}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Age</div>
                                <div class="col-md-8">{{data.age}}</div>
                                <div class="col-md-2">{{data.age_other}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Fee</div>
                                <div class="col-md-8">{{data.fee}}</div>
                                <div class="col-md-2">{{data.fee_detail}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Limit</div>
                                <div class="col-md-8">{{data.limit}}</div>
                                <div class="col-md-2">{{data.limit_detail}}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Equipment</div>
                                <div class="col-md-10">{{data.equipment}}</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
