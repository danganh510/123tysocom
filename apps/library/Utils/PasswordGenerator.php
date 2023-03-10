<?php
namespace Score\Utils;
use Phalcon\Mvc\User\Component;

class PasswordGenerator extends Component
{
    //generate password
    public static function salt($lenght=12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function encodePass($pass)
    {
        $passSalt = self::salt();
        $iteration = 10000;//default in Azerbaijan Immigration Services Teamrp
        $hashPass = $iteration."\$".$passSalt."\$".base64_encode(hash_pbkdf2("sha256", $pass, $passSalt, $iteration, 32, true));
        return $hashPass;
    }
    public static function decodePass($pass,$salt,$iteration)
    {
        $checkHash = base64_encode(hash_pbkdf2("sha256", $pass, $salt, $iteration, 32, true));
        return $checkHash;

    }

	//generate password
	public function generatePassword($length=10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+<>?,./;:|{}[]';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		$password = $this->encryptPassword($randomString);
		return $password;
	}
	//encrypt password
	public function encryptPassword( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		return( $qEncoded );
	}
	//descrypt password
	public function decryptPassword( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
		return( $qDecoded );
	}
}

