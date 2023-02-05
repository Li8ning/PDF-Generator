<?php
global $bgr_img;
function read_data($file,$input_data){
  $GLOBALS['bgr_image'] = $input_data['bg_image'];
  echo '<br>Reading Data<br>';
  clearstatcache();
  $h = fopen($file, "r");
  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
    $cert_data[] = $data;
  }
  fclose($h);
  echo 'Generating PDFs<br>';
  generate_certificate($cert_data,$input_data);
}
add_action('read_cert_data','read_data','',2);

// Plugin dir path
$path = dirname(__FILE__);
// Including TCPDF library
require($path.'/library/TCpdf/tcpdf.php');
// Custom class to set background image
class MYPDF extends TCPDF {
  //Page header
  public function Header() {
      // get the current page break margin
      $bMargin = $this->getBreakMargin();
      // get current auto-page-break mode
      $auto_page_break = $this->AutoPageBreak;
      // disable auto-page-break
      $this->SetAutoPageBreak(false, 0);
      $this->SetMargins(0,0,0);
      // set bacground image Image(  $file,   $x = '',   $y = '',   $w,   $h,   $type = '',   $link = '',   $align = '',   $resize = false,   $dpi = 300,   $palign = '',   $ismask = false,   $imgmask = false,   $border,   $fitbox = false,   $hidden = false,   $fitonpage = false,   $alt = false,   $altimgs = array())
      $this->Image($GLOBALS['bgr_image'], 0, 0, 297,210, 'JPG', '', '', false,300, '', false, false, 0, false, false, true);
      // restore auto-page-break status
      $this->SetAutoPageBreak($auto_page_break, $bMargin);
      $this->SetMargins(0,85,0);
      // set the starting point for the page content
      $this->setPageMark();
  }
}

function generate_certificate($cert_data,$input_data){
  $path = dirname(__FILE__);
  // Extend the TCPDF class to create custom Header and Footer
  foreach ($cert_data as $key => $value) {
    if ($key != 0) {      
      // User defined pdf Customization
      $general_font_size = ($input_data['general_font_size'] == null) ? 14 : $input_data['general_font_size'];
      $attendee_font_size = ($input_data['attendee_font_size'] == null) ? 50 : $input_data['attendee_font_size'];
      $event_title_font_size = ($input_data['event_title_font_size'] == null) ? 18 : $input_data['event_title_font_size'];

      // create new PDF document
      $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      // set document information
      $pdf->SetCreator('Ananda Vak Sols LLP');
      $pdf->SetAuthor('Ananda Vak Sols LLP');
      $pdf->SetTitle('Certificate of Attendance - '.$value[1]);
      $pdf->SetSubject('Certificate of Attendance - '.$value[1]);
      // set default monospaced font
      $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
      // set margins
      $pdf->SetHeaderMargin(0);
      $pdf->SetFooterMargin(0);
      $pdf->SetMargins(50,85,50, true);
      // remove default footer
      $pdf->setPrintFooter(false);
      // set auto page breaks
      $pdf->SetAutoPageBreak(false, 0);
      // set image scale factor
      $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
      // ---------------------------------------------------------
      // add a page
      $pdf->AddPage('L','A4');
      $pdf->Image($input_data['org_image'], 112, 31, 67.01,15.44, 'PNG', '', '', false,300, '', false, false, 0, false, false, false);
      // set font
      $pdf->SetFont('Helvetica', '', $general_font_size);
      // Print a text
      $pdf->Cell(0, 5, 'This is to recognize', 0, 1, 'C', 0, '', 0);
      // set font
      $pdf->SetFont('Helvetica', '', $attendee_font_size);
      // Attendars name
      $pdf->Cell(0, 5, $value[0], 0, 1, 'C', 0, '', 0);
      // set font
      $pdf->SetFont('Helvetica', '', $general_font_size);
      $pdf->Cell(0, 5, 'For attending the webinar on', 0, 1, 'C', 0, '', 0);
      // set font
      $pdf->SetFont('Helvetica', 'B', $event_title_font_size);
      // Event Title
      $pdf->MultiCell(0, 5, $value[1], 0, 'C', 0, 1, '', '', true);
      // set font
      $pdf->SetFont('Helvetica', '', $general_font_size);
      // Event Date
      $pdf->Cell(0, 8, $value[2], 0, 2, 'C', 0, '', 0);
      $pdf_file_path = $path.'/certificate/'.$value[0].'-'.date('ymdHis').'.pdf';
      $pdf_file_name = basename($pdf_file_path);
      $pdf->Output($pdf_file_path,'F');
      if (file_exists($pdf_file_path)) {
        echo 'File Exists.<br>Sendind Mail.<br>';
        // Email Details
        $to = $value[3];
        $sub = 'Certificate of Attendance for '.$value[0];
        // checking if email body is entered
        if(!empty($input_data['email_body'])){
          // Replace the custom variables with their values
          $input_data['email_body'] = str_replace("{{variable1}}", $value[0], $input_data['email_body']);
          $input_data['email_body'] = str_replace("{{variable2}}", $value[1], $input_data['email_body']);
          $input_data['email_body'] = str_replace("{{variable3}}", $value[2], $input_data['email_body']);
          $msg = htmlspecialchars_decode($input_data['email_body']);
          // print_r($input_data['email_body']);
        }
        else{
          // Default MSG        
          $msg = '<table style="width: 1245px; height: 180px;" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td style="background-color: #fff;" valign="top" bgcolor="F5F5F5" align="left">
                          <table style="width: 886px;" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td style="padding: 0px 15px 20px;" bgcolor="#ffffff">
                                  <table style="width: 100%;" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td style="padding: 10px 0; text-align: justify; font-family: Arial, "sans-serif"; font-size: 14px; line-height: 18px; color: #000;">
                                          <p style="margin: 0px;">Dear '.$value[0].',</p>
                                          <p style="margin-top: 6px;">EFY Group would like to thank you for attending the webinar titled '.$value[1].' on '.$value[2].'. We hope you will be able to benefit from the knowledge gained during this webinar, and look forward to seeing you at the next event.</p>
                                          <p style="margin-top: 6px;">We truly appreciate your support.</p>
                                          <p style="margin-top: 6px;">Thank you!</p>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>';
        }
        $headers[] = 'From: '.$input_data['sender_name'].' <'.$input_data['sender_email'].'>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        if(send_mail($to,$sub,$msg,$headers,$pdf_file_path)){
          echo 'Mail sent successfully<br>';
        } 
      }
      echo $value[0].' PDF Generated -> <a href="'.get_site_url().'/wp-content/plugins/PDF Generator - AV Sols/certificate/'.$pdf_file_name.'" target="_blank">Open</a><br><br>';
    }
  }
}

function send_mail($to,$sub,$msg,$headers,$attachment){
  return wp_mail($to,$sub,$msg,$headers,$attachment);
}

// Clear Data
function cleardata(){
  echo "Clearing Data<br>";
  $path = dirname(__FILE__);
  // Clearing Certificate data
  echo "Clearing Cert<br>";
  $files = glob($path.'/data/*'); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file)) {
      unlink($file); // delete file
    }
  }
  // Clearing PDFs
  echo "Clearing PDFs<br>";
  $files = glob($path.'/certificate/*'); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file)) {
      unlink($file); // delete file
    }
  }
}
// add_action('clear_all_data','read_data','',2);