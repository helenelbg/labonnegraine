<?php
?><script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<style>
    div {
        color: #4a535e;
    }
    .clear {clear: both;}
    .div_form {
        clear: both;
        margin-bottom: 10px;
        line-height: 30px;
    }
    input,select,textarea {
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: none;
        color: #555;
        height: 30px;
        width: 400px;
    }
    textarea {
        height: 5em;
    }
    input.chk {
        width: auto;
        height: auto;
    }
    select.multiple {
        width: 280px;
        float: left;
    }
    input.for_big,select.for_big {
        margin-left: 210px;
    }

    .colspan {
        margin-left: 310px;
    }

    .btn {
        background-color: #EF2F37;
        border: 0px;
        color: #fff;
        border-radius: 3px;
        line-height: 31px;
        padding: 6px 15px;
        transition: all 0.2s ease-out 0s;
        cursor: pointer;
    }
    .btn:focus,.btn:hover,.btn:active {
        background-color: #EF2F37;
        color: #fff;
    }

    .btn.darkgrey {
        background-color: #7b7b7b;
        color: #fff;
    }
    .btn.darkgrey:focus,.btn.darkgrey:hover,.btn.darkgrey:active {
        background-color: #929292;
        color: #fff;
    }
    .btn.lightgrey {
        background-color: #c8c8c8;
        color: #fff;
        cursor: auto;
    }
    .btn.lightgrey:focus,.btn.lightgrey:hover,.btn.lightgrey:active {
        background-color: #c8c8c8;
        color: #fff;
        cursor: auto;
    }

    .btn.center.big {
        position: relative;
        left: 50%;
        margin-left: -200px;
        width: 400px;
    }

    .form_label {
        float: left;
        width: 300px;
        height: 30px;
        line-height: 30px;
        text-align: right;
        margin-right: 10px;
    }
    .form_label.big {
        float: none;
        width: 100%;
        text-align: left;
        margin-right: 0px;
    }

    a {
        color: #428bca;
        text-decoration: none;
    }
    a:focus,a:hover,a:active {
        text-decoration: underline;
    }

    .clickable {cursor: pointer}

    .circle {
        width:50px;
        height:50px;
        border-radius:25px;
        font-size:14px;
        line-height:50px;
        text-align:center;
        background:#929292;
        color: #ffffff;
        position: relative;
        left: 50%;
        margin-left: -25px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .title {
        font-weight: bold;
        font-size: 14px;
        margin-left: 20px;
        color: #222;
    }

    .message {
        width: 80%;
        margin-left: 10%;
        border-radius: 8px;
    }
    .message.stripes {
        background-image: url('lib/img/white_stripes.png');
    }

    .message div {
        padding: 30px;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }

    /*
    STEPS
    */
    .steps {
        position: relative;
    }
    .steps .step {
        position: relative;
        display: block;
        float: left;
        height: 40px;
        line-height: 40px;
        border: 0px;
        text-align: center;
        text-transform: uppercase;
    }
    .steps .step .arrow {
        position: absolute;
        display: block;
        float: left;
        border-style:solid;
        border-width:20px;
        width:0;
        height:0;
        top: 0px;
        left: 0px;
    }

    .steps .step_1 {
        background: #C5C5C5;
    }

    .steps .step_2 {
        background: #DDDDDD;
    }
    .steps .step_2 .arrow {
        border-color: #DDDDDD #DDDDDD #DDDDDD #C5C5C5;
    }

    .steps .step_3 {
        background: #ECECEC;
    }
    .steps .step_3 .arrow {
        border-color: #ECECEC #ECECEC #ECECEC #DDDDDD;
    }

    .steps .step_4 {
        background: #F4F4F4;
    }
    .steps .step_4 .arrow {
        border-color: #F4F4F4 #F4F4F4 #F4F4F4 #ECECEC;
    }

    /*
    STEP 1
     */
    .steps.actual_step_1 .step_1 {
        background: #EF2F37;
        color: #ffffff;
    }
    .steps.actual_step_1 .step_2 .arrow {
        border-color: #DDDDDD #DDDDDD #DDDDDD #EF2F37;
    }


    /*
    STEP 2
     */
    .steps.actual_step_2 .step_1 {
        background: #878787;
        color: #ffffff;
    }
    .steps.actual_step_2 .step_2 {
        background: #EF2F37;
        color: #ffffff;
    }
    .steps.actual_step_2 .step_2 .arrow {
        border-color: #EF2F37 #EF2F37 #EF2F37 #878787;
    }
    .steps.actual_step_2 .step_3 .arrow {
        border-color: #ECECEC #ECECEC #ECECEC #EF2F37;
    }


    /*
    STEP 3
     */
    .steps.actual_step_3 .step_1 {
        background: #919191;
        color: #ffffff;
    }
    .steps.actual_step_3 .step_2 {
        background: #878787;
        color: #ffffff;
    }
    .steps.actual_step_3 .step_2 .arrow {
        border-color: #878787 #878787 #878787 #919191;
    }

    .steps.actual_step_3 .step_3 {
        background: #EF2F37;
        color: #ffffff;
    }
    .steps.actual_step_3 .step_3 .arrow {
        border-color: #EF2F37 #EF2F37 #EF2F37 #878787;
    }


</style>