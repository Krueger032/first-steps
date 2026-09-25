<?

namespace Linkor\Redirect;

class Helper
{
	static function encodeCyrillicOnly($url)
	{
		return preg_replace_callback(
			'/[\x{0400}-\x{04FF}]+/u',
			function ($m) {
				return rawurlencode($m[0]);
			},
			$url
		);
	}
}
