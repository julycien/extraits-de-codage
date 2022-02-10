<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>

            .header {
                position: fixed;
                background-color: black;
                color: white;
                top: 0px;
                left: 0px;
                line-height: 80px;
                font-family: Verdana, Tahoma, Helvetica Neue, sans-serif;
                min-width: 100%;
                margin: 0;
                z-index: 20;
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                font-size: 1.5em;

            }

            .nav {
                color: white;
                text-decoration: none;
                display: block;
            }

            a:hover {
                color: red;
                text-decoration: underline;
                display: block;
                font-weight: bold;
            }

            a:link {
                color: white;
                text-decoration: none;
                display: block;
            }

            a:visited {
                color: white;
                display: block;
            }

            a:active {
                color: grey;
                display: block;
            }

            .itemmenu {
                display: flex;
                align-items: flex-start;
            }
            #logoheader {
                height: 30px;
            }

            #wrap {
                position: fixed;
                font-family: sans-serif;
                font-size: 21px;
                line-height: 1.6;
                top: 0;
                left: 0;
                display: flex;
                color: white;
                transition: transform .4s cubic-bezier(0.25, .1, .25, 1);
                z-index: 0;
            }

            #wrap:not(:target) {
                transform: translate3d(-200px, 0, 0);
                background-color: none;
                z-index: 0;
            }

            #wrap:target {
                transform: translate3d(40px, 0, 0);
                z-index: 0;
            }

            #open,
            #close {
                height: 44px;
                text-align: right;
                display: block;
                margin-right: -40px;
                z-index: 0;
            }

            #wrap:target #open,
            #wrap:not(:target) #close {
                display: none;
            }

            #wrap:target #open {
                display: none;
            }

            .headerwrap {
                width: 200px;
                display:inline-block;
                vertical-align:top;
                display: none;
                z-index: 0;
                padding-top: 10px;
                z-index: 0;
            }
            .fondnoirmenu {
                background-color: black;
                margin: -50px 0 0 -40px;
                padding: 5%;
                z-index: 0;
                border-radius: 1em;
            }


            @media screen and (max-width: 64em) {

                #logoheader {
                    height: 30px;
                }
            }
            @media screen and (max-width: 50em) {

                #logoheader {
                    height: 20px;
                }
                .header {
                    display: none;
                }
                .headerwrap {
                    display: block;
                }
            }
            @media screen and (max-width: 30em) {

                .header {
                    display: none;
                }
                .headerwrap {
                    display: block;
                }
            }

        </style>
    </head>
    <body>
        <div id="wrap">
            <header class="headerwrap">
                <nav class="nav">
                    <a href="#wrap" id="open">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="34px" height="27px" viewBox="0 0 34 27" enable-background="new 0 0 34 27" xml:space="preserve">
                        <rect fill="#FFFFFF" width="34" height="4"/>
                        <rect y="11" fill="#FFFFFF" width="34" height="4"/>
                        <rect y="23" fill="#FFFFFF" width="34" height="4"/>
                        </svg>
                    </a>
                    <div class="fondtransarentmenu">
                        <a href="#" id="close">×</a>
                    </div>
                    <div class="fondnoirmenu">
                        <a href="../boundaries/Index.php"><img src="../msrBikeAdmin/boundaries/images/logo3.jpg" alt="logo msrBike" height="40px" id="logoheader"  ></a>
                        <a href="../controls/FrontAtelierCTRL.php"><i>Atelier</i></a>
                        <a href="../controls/FrontOccasionsCTRL.php"><i>Motos Occasion</i></a>
                        <a href="../controls/FrontNeuvesCTRL.php"><i>Motos Neuves</i></a>
                        <a href="../controls/FrontSendMessageCTRL.php"><i>Contact</i></a>
                    </div>
                </nav>
            </header>

        </div>

        <div class="header">
            <div class="itemmenu">
                <a href="../boundaries/Index.php"><img src="../msrBikeAdmin/boundaries/images/logo3.jpg" alt="logo msrBike" id="logoheader"  ></a>
            </div>
            <div class="itemmenu">
                <a href="../controls/FrontAtelierCTRL.php"><i>Atelier</i></a>
            </div>
            <div class="itemmenu">
                <a href="../controls/FrontOccasionsCTRL.php"><i>Motos Occasion</i></a>
            </div>
            <div class="itemmenu">
                <a href="../controls/FrontNeuvesCTRL.php"><i>Motos Neuves</i></a>
            </div>
            <div class="itemmenu">
                <a href="../controls/FrontSendMessageCTRL.php"><i>Contact</i></a>
            </div>
        </div>

        <div class="modal">

        </div>
    </body>
</html>
