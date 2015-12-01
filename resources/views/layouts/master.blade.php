<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>@yield("title")</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/landing-page.css" rel="stylesheet"/>

    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

</head>
<body class="landing-page landing-page1">
<nav class="navbar navbar-transparent navbar-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button id="menu-toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar bar1"></span>
                <span class="icon-bar bar2"></span>
                <span class="icon-bar bar3"></span>
            </button>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="example" >
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="https://github.com/mtahv3/mwsh-ical">
                        <i class="fa fa-github-square"></i>
                        Contribute
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
</nav>
<div class="wrapper">
    <div class="parallax filter-gradient blue" data-color="blue">
        <div class="parallax-background">
            <img class="parallax-background-image" src="assets/img/mwsh.jpg">
        </div>
        <div class= "container">
            <div class="row">
                <div class="col-md-5 hidden-xs">
                    <div class="parallax-image">

                        <video webkit-playsinline="" loop="loop" muted="muted" autoplay="autoplay" preload="auto" poster="//i.imgur.com/5RwhGBjh.jpg" style="width: 300px; height: 533px;">
                            <source type="video/webm" src="//i.imgur.com/5RwhGBj.webm"></source>
                            <source type="video/mp4" src="//i.imgur.com/5RwhGBj.mp4"></source>
                        </video>

                        {{--<img class="phone" src="assets/img/mov2.gif" style="margin-top: 20px"/>--}}
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-1">
                    <div class="description">
                        <h2>Subscribe to Your Schedule</h2>
                        <br>
                        <h5>It's inevitable! Tom will change your schedule at some point during the season. However, sync your teams' schedule to your calendar and never have to worry about last minute changes again.</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section section-gray section-clients">
        <div class="container text-center">
            <h4 class="header-text">Never Miss A Game</h4>
            <p>
                Watching your team skate off the rink as the buzzer sounds at the end of the 2nd, right as you're walking in for a game at the original time is no fun. Don't miss out again.<br>
            </p>
        </div>
    </div>
    <div class="section section-presentation">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="description">
                        <h4 class="header-text">It's Simple!</h4>
                        <div class="form-group">
                            <p>
                                <label for="selectLeague">Select your league</label>
                                <select class="form-control" id="selectLeague">
                                    <option value="0">Select your league</option>
                                </select>
                            </p>
                            <p>
                                <label for="selectTeam">Select your team</label>
                                <select class="form-control" id="selectTeam">

                                </select>
                            </p>
                            <p>
                                <button type="button" id='generateButton' class="btn btn-info">Generate iCal Link</button>
                            </p>
                            <p>
                                <label for="url">Calendar Link</label>
                                {{--<div class="input-group">--}}
                                    {{--<input id="url">--}}

                                    {{--<span class="input-group-btn">--}}
                                        {{--<button class="btn" data-clipboard-target="#url">--}}
                                            {{--<img src="assets/img/clippy.svg" width="13" alt="Copy to clipboard">--}}
                                        {{--</button>--}}
                                    {{--</span>--}}
                                {{--</div>--}}

                            <div class="input-group">
                                <input type="text" class="form-control" id="url">
                                <span class="input-group-btn">
                                    <button class="btn btn-secondary clipboard" data-clipboard-target="#url" type="button" data-toggle="tooltip" title="Copy to Clipboard" data-placement="bottom">Copy <i class="fa fa-files-o"></i></button>
                                </span>
                            </div>
                            </p>
                        </div>
                        <p>
                    </div>
                </div>
                <div class="col-md-5 col-md-offset-1 hidden-xs">

                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="copyright">
                &copy; 2015 Matt Allen, made in frustration. This site is in no way affiliated with Midwest Sport Hockey or Edgar M. Queeny County Park.
            </div>
        </div>
    </footer>
</div>

</body>
<script src="assets/js/jquery-1.10.2.js" type="text/javascript"></script>
<script src="assets/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.js" type="text/javascript"></script>
<script src="assets/js/awesome-landing-page.js" type="text/javascript"></script>
<script src="assets/js/clipboard.min.js" type="text/javascript"></script>
<script src="assets/js/mwsh.js" type="text/javascript"></script>
</html>