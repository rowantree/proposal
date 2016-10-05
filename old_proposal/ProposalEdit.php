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
<body>
	<div ng-app="ProposalEditApp">
		<div ng-controller="RegController as reg">


            <div class="row">

                <div class="col-md-2 scrollable-menu scrollbar" role="menu">
                    <div ng-repeat="data in reg.proposalData" style="padding: 2px 2px 2px 2px;">
                        <button class="btn-block" ng-click="reg.ShowProposal($index)">{{data.LegalName}}</button>
                    </div>
                </div>

                <div class="col-md-10">

                    <div class="row">
                        <div class="col-md-12 alert-success">{{reg.UserMsg}}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">Legal Name</div>
                        <div class="col-md-10">{{reg.ShowData.LegalName}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">Program Name</div>
                        <div class="col-md-10">{{reg.ShowData.ProgramName}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">Email</div>
                        <div class="col-md-10">{{reg.ShowData.EmailAddress}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">Phone</div>
                        <div class="col-md-10">{{reg.ShowData.TelephoneNumber}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">Unavailable</div>
                        <div class="col-md-10">{{reg.ShowData.UnavailableTimes}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">Biography</div>
                        <div class="col-md-10">{{reg.ShowData.Biography}}</div>
                    </div>
                    <div ng-repeat="data in reg.ShowData.OtherPeople">
                        <div class="row"><div class="col-md-12"><hr></div></div>
                        <div class="row">
                            <div class="col-md-2"><b>Legal Name</b></div>
                            <div class="col-md-10">{{data.LegalName}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Program Name</div>
                            <div class="col-md-10">{{data.ProgramName}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Biography</div>
                            <div class="col-md-10">{{data.Bio}}</div>
                        </div>
                    </div>
                    <div ng-repeat="data in reg.ShowData.Presentations">
                        <div class="row"><div class="col-md-12"><hr></div></div>
                        <div class="row">
                            <div class="col-md-2"><b>Title</b></div>
                            <div class="col-md-10">{{data.Title}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Description</div>
                            <div class="col-md-10">{{data.Presentation}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Type</div>
                            <div class="col-md-10">{{data.PresentationType}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Space</div>
                            <div class="col-md-8">{{data.SpacePreference}}</div>
                            <div class="col-md-2">{{data.SpacePreferenceOther}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Audience</div>
                            <div class="col-md-10">{{data.TargetAudience}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Time Pref.</div>
                            <div class="col-md-10">{{data.TimePreference}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Age</div>
                            <div class="col-md-8">{{data.Age}}</div>
                            <div class="col-md-2">{{data.AgeOther}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Fee</div>
                            <div class="col-md-8">{{data.Fee}}</div>
                            <div class="col-md-2">{{data.FeeDetail}}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">Limit</div>
                            <div class="col-md-8">{{data.Limit}}</div>
                            <div class="col-md-2">{{data.LimitDetail}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
