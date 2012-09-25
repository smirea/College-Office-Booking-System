<?php
//include "libmail.php";


function sendMail($to, $subject, $from, $message){
	$message = emailBody($message);
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: $from" . "\r\n";
	$bcc = explode(',', $to);
	for($i=1; $i<count($bcc); ++$i)	$headers .= "BCC: ".$bcc[$i]." \r\n";
	
	return mail($bcc[0], $subject, $message, $headers) ? true : false;
}

// CUSTOMIZE !!!!!!!!!!!
// ALSO INCLUDE LIBMAIL.PHP BEFORE USAGE
/*
function sendMailAttachment($to, $subject, $from, $message, $imagePath = ''){
	$m= new Mail; // create the mail
	$m->From( $from );
	$m->To( $to );
	$m->Subject( $subject );
	$m->Attach( $imagePath, "image/png", "inline" ) ;	// attach a file of type image/gif to be displayed in the message if possible
	
	$file = "mailLog/order_".time()."_".date('Y.m.d-g.iAO').".html";
	file_put_contents($file, $message);
	$m->Body( " " );
	$m->Attach( $file, "text/html", "inline" );

	$m->Send();	// send the mail
	return 'To be added';
}
*/
function emailBody($message){
	return '
<table width="550" cellspacing="0" cellpadding="0" border="0" style="">
	<tbody>
		<tr>
			<td><img src="http://booking.tak3r.com/images/mercator_logo_medium.jpg" height="130" /></td>
			<td style=" text-align:center; ">
				<h1 style="color:darkblue; font-family:georgia; font-size:34pt; margin:0 auto; padding:0;">Mercator<br /> College Office</h1>
			</td>
		 </tr>
		 <tr>
			<td colspan="2" style="padding:5px 0 0 0;">
				<table width="550" cellspacing="0" cellpadding="0" border="0" style="border-top:3px solid #dadada;">
					<tbody>
						<tr>
							<td style="padding:10px; border-top:2px solid #eaeaea; font-family:trebuchet, arial;">
								
								'.$message.'
								
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>	
	</tbody>
</table>
	';
}

?>
