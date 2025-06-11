<?php $is_uploaded = false;
$errors = array();

$dir = _PS_IMG_DIR_.'/banner/';
$gmassiTokenName = 'SC_GMA_INI';
session_start();

if (!empty($_FILES['picture']) && count($_FILES['picture']) > 0)
{
    if(!isset($_POST['gmassitoken'])
        || $_POST['gmassitoken'] !== $_SESSION[$gmassiTokenName])
    {
        $errors[] = _l('Invalid Form');
    }
    else
    {
        unset($_SESSION[$gmassiTokenName]);
        for ($num = 1; $num <= 5; ++$num)
        {
            if (!empty($_FILES['picture']['tmp_name'][$num]))
            {
                $error_picture = array();

                $tmp_name = $_FILES['picture']['tmp_name'][$num];
                $name = $_FILES['picture']['name'][$num];
                $fileInfo = array(
                    'name' => $name,
                    'type' => $_FILES['picture']['type'][$num],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['picture']['error'][$num],
                    'size' => $_FILES['picture']['size'][$num]
                );

                if (!isImage($fileInfo,true))
                {
                    $error_picture[] = _l('Banner').' '.$num.' : '._l('the file is not a picture.');
                }

                $new_name = preg_replace("/[^a-zA-Z0-9\/_|+ -.]/", '', $name);
                $new_name = strtolower(trim($new_name, '-'));
                $new_name = preg_replace("/[\/_|+ -]+/", '-', $new_name);

                if (empty($error_picture))
                {
                    if (!move_uploaded_file($tmp_name, $dir.$new_name))
                    {
                        $errors[] = _l('Banner').' '.$num.' : '._l('an error occurred. Please try again.');
                    }
                    else
                    {
                        $banner = new SCAffBanner();
                        $banner->name = '';
                        $banner->image = $new_name;
                        $banner->actire = 0;
                        $banner->date_add = date('Y-m-d H:i:s');
                        $banner->save();
                        $is_uploaded = true;
                    }
                }
                else
                {
                    $errors = array_merge($errors, $error_picture);
                }
            }
        }
    }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
<style type="text/css">

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
    display: block;
}
body {
    line-height: 1;
    color: #000000;
    font-family: Tahoma;
}
ol, ul {
    list-style: none;
}
blockquote, q {
    quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content: '';
    content: none;
}
table {
    border-collapse: collapse;
    border-spacing: 0;
}

.btn {
    background: none repeat scroll 0 0 #e2edf2;
    border: 1px solid #A4BED4;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
    font-weight: bold;
    cursor: pointer;
}
.btn.submit {float: right;}
.btn.reset {float: left;}

.form {
font-size: 11px;
color: #000000;
padding: 20px;
width: 400px;
}

.error {
padding: 10px;
border: 1px solid #ce0000;
color: #ce0000;
background: #ffe4e4;
margin-bottom: 1em;
}

</style>
<script src="<?php echo SC_JQUERY; ?>" type="text/javascript"></script>
<script type="text/javascript">
SC_ID_LANG=<?php echo $sc_agent->id_lang; ?>;

<?php if ($is_uploaded) { ?>
parent.displayBanner();
<?php } ?>
</script>
</head>
<body>

<div class="form">
    <form method="post" action="" enctype="multipart/form-data">
    
        <?php
        $gmassiToken = md5(uniqid(rand(),true), false);
        $_SESSION[$gmassiTokenName] = $gmassiToken;
        if (!empty($errors))
        {
            echo '<div class="error">';
            foreach ($errors as $error)
            {
                echo $error.'<br/>';
            }
            echo '</div>';
        }
        ?>
        <input type="hidden" name="gmassitoken" value="<?php echo $gmassiToken; ?>"/>
        <label><?php echo _l('Banner'); ?> 1 :</label>
        <input type="file" name="picture[1]" value="" />
        <br/><br/>
    
        <label><?php echo _l('Banner'); ?> 2 :</label>
        <input type="file" name="picture[2]" value="" />
        <br/><br/>
    
        <label><?php echo _l('Banner'); ?> 3 :</label>
        <input type="file" name="picture[3]" value="" />
        <br/><br/>
    
        <label><?php echo _l('Banner'); ?> 4 :</label>
        <input type="file" name="picture[4]" value="" />
        <br/><br/>
    
        <label><?php echo _l('Banner'); ?> 5 :</label>
        <input type="file" name="picture[5]" value="" />
        <br/><br/>
    
        <button type="submit" class="btn submit"><?php echo _l('Upload'); ?></button>
        <div style="clear: both;"></div>
    </form>
</div>

</body>
</html>