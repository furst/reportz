<?php

class Email {

	/**
	 * @var string
	 */
	private static $fromEmail = 'andreas.furst@outlook.com';

	/**
	 * @param  string $email
	 * @param  string $reportName
	 * @param  string $code
	 * @param  string $uniqueName
	 */
	public static function send($email, $reportName, $code, $uniqueName) {

		$to = $email;

		$subject = "Kod till rapport: $reportName";

		$message =
"
Hej! Tack för dina svar till rapporten $reportName

Här är din kod om du vill redigera dina testfall: $code

Eller använd denna länk för att direkt komma till rapporten: http://www.flexboard.se/public/edit-report/$uniqueName/$code
";

		$from = self::$fromEmail;

		$headers = "From:" . $from;

		mail($to,$subject,$message,$headers);
	}
}