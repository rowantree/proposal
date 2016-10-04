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

                <div class="col-md-2 scrollable-menu scrollbar" role="menu">
                    <div ng-repeat="data in reg.proposalData" style="padding: 2px 2px 2px 2px;">
                        <button class="btn-block" ng-click="reg.ShowProposal($index)">{{data.legal_name}}</button>
                    </div>
                </div>

                <div class="col-md-10">

                    <div class="row">
                        <div class="col-md-12 alert-success">{{reg.UserMsg}}</div>
                    </div>


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
                            <div class="col-md-10"><input class="form-control" type="text" ng-model="data.title"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Location</div>
                            <div class="col-md-10"><input class="form-control" type="text" ng-model="data.location"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Time</div>
                            <div class="col-md-10"><input class="form-control" type="text" ng-model="data.time"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">Description</div>
                            <div class="col-md-10"><textarea class="form-control" ng-model="data.presentation"></textarea></div>
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
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>