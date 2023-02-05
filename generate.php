<?php

$content = '
<style>
.upload-wrapper,#delete_data_btn{margin-bottom:10px;}
fieldset{
    display: block;
    margin-inline-start: 2px;
    margin-inline-end: 2px;
    margin-bottom: 5px;
    padding-block-start: 0.75em;
    padding-inline-start: 0.75em;
    padding-inline-end: 0.75em;
    padding-block-end: 0.625em;
    min-inline-size: min-content;
    border-width: 2px;
    border-style: groove;
    border-color: threedface;
    border-image: initial;
}
fieldset legend{
    font-weight: bold;
    font-size: 18px;
}
#email_body_input{
    display:block;
    margin-top: 10px;
    width:100%;
}
#email_body{
    display:none;
}
</style>
<h1>Generate PDF</h1>
<button name="clearData" id="delete_data_btn">Flush the PDF Generator\'s Cache</button>
<form method="POST" action="" enctype="multipart/form-data">
    <p>Required fields are followed by <strong><abbr title="required">*</abbr></strong></p>
    <fieldset>
        <legend>Sender\'s Configurations</legend>
        <div class="upload-wrapper">
            <label class="sender_name" for="sender_name">Sender\'s Name <abbr title="required" aria-label="required">*</abbr></label>
            <input type="text" id="sender_name" name="sender_name" required>
        </div>
        <div class="upload-wrapper">
            <label class="sender_email" for="sender_email">Sender\'s Email <abbr title="required" aria-label="required">*</abbr></label>
            <input type="email" id="sender_email" name="sender_email" required>
        </div>
    </fieldset>
    <fieldset>
        <legend>PDF Customizations</legend>
        <div class="upload-wrapper">
            <label class="org-image" for="org_image">Organiser\'s Logo (Link) [Recommended: 200x55 px; Required: PNG] <abbr title="required" aria-label="required">*</abbr></label>
            <input type="text" id="org_image" name="org_image" required>
        </div>
        <div class="upload-wrapper">
            <label class="bg-image" for="bg_image">Certificate Design/Background Image (Link) [Recommended: 1122x793 px; Required: JPG] <abbr title="required" aria-label="required">*</abbr></label>
            <input type="text" id="bg_image" name="bg_image" required>
        </div>
        <div class="upload-wrapper">
            <label for="general_font_size">General Font Size [Default: 14px]</label>
            <input type="number" id="general_font_size" name="general_font_size" min="1" max="20" step="1">
        </div>
        <div class="upload-wrapper">
            <label for="attendee_font_size">Attendee Font Size [Default: 50px]</label>
            <input type="number" id="attendee_font_size" name="attendee_font_size" min="1" max="50" step="1">
        </div>
        <div class="upload-wrapper">
            <label for="event_title_font_size">Event Title Font Size [Default: 18px]</label>
            <input type="number" id="event_title_font_size" name="event_title_font_size" min="1" max="20" step="1">
        </div>
    </fieldset>
    <fieldset>
        <legend>Data Input</legend>
        <div class="upload-wrapper">
            <label class="file-name" for="file-upload">Upload Contacts (Headers: Name, Event, Date, Email) [Required: CSV] <abbr title="required" aria-label="required">*</abbr></label>
            <input type="file" id="file-upload" name="uploadedFile" required>
        </div>
        <div class="upload-wrapper">
            <label for="email_body">Email Body [Available variable: <b><i>Name->{{variable1}}, Event Title->{{variable2}}, Event Date->{{variable3}}</b></i>] <abbr title="required" aria-label="required">*</abbr></label>
            <textarea name="email_body" id="email_body_input" rows="10"><table style="width: 1245px; height: 180px;" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="background-color: #fff;" valign="top" bgcolor="F5F5F5" align="left"><table style="width: 886px;" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="padding: 0px 15px 20px;" bgcolor="#ffffff"><table style="width: 100%;" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="padding: 10px 0; text-align: justify; font-family: Arial, "sans-serif"; font-size: 14px; line-height: 18px; color: #000;"><p style="margin: 0px;">Dear {{variable1}},</p><p style="margin-top: 6px;">EFY Group would like to thank you for attending the webinar titled {{variable2}} on {{variable3}}. We hope you will be able to benefit from the knowledge gained during this webinar, and look forward to seeing you at the next event.</p><p style="margin-top: 6px;">We truly appreciate your support.</p><p style="margin-top: 6px;">Thank you!</p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></textarea>
        </div>
    </fieldset>
    <input type="submit" name="uploadBtn" value="Upload & Send Emails" />
</form>';
echo $content;

if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload & Send Emails') {
    if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
        // get details of the uploaded file
        $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
        $fileName = $_FILES['uploadedFile']['name'];
        $fileSize = $_FILES['uploadedFile']['size'];
        $fileType = $_FILES['uploadedFile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5($fileName).'.'. $fileExtension; //sanitize the filename
        $allowedfileExtensions = array('csv');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // directory in which the uploaded file will be moved
            if (!file_exists(plugin_dir_path(__FILE__).'data/')) {
                mkdir(plugin_dir_path(__FILE__).'data/', 0777, true);
            }
            $uploadFileDir = plugin_dir_path(__FILE__).'data/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path))
            {
                echo 'CSV File is successfully uploaded.';
                $input_data['org_image'] = $_POST['org_image'];
                $input_data['bg_image'] = $_POST['bg_image'];
                $input_data['sender_name'] = $_POST['sender_name'];
                $input_data['sender_email'] = $_POST['sender_email'];
                $input_data['general_font_size'] = $_POST['general_font_size'];
                $input_data['attendee_font_size'] = $_POST['attendee_font_size'];
                $input_data['event_title_font_size'] = $_POST['event_title_font_size'];
                $input_data['email_body'] = htmlspecialchars($_POST['email_body']);
                do_action(read_data($dest_path,$input_data));
                exit();
            }
            else
            {
                echo 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
            }
        }
        else{
            echo 'Unsupported file. Please try again.';
            exit();
        }
    }
}