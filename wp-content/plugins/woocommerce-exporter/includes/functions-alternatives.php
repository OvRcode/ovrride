<?php
if( !function_exists( 'escape_csv_value' ) ) {
	function escape_csv_value( $value ) {

		$value = str_replace( '"', '""', $value ); // First off escape all " and make them ""
		$value = str_replace( PHP_EOL, ' ', $value );
		return '"' . $value . '"'; // If I have new lines or commas escape them

	}
}

if( !function_exists( 'mb_list_encodings' ) ) {

	function mb_list_encodings() {

		$list_encoding = array( "pass", "auto", "wchar", "byte2be", "byte2le", "byte4be", "byte4le", "BASE64", "UUENCODE", "HTML-ENTITIES", "Quoted-Printable", "7bit", "8bit", "UCS-4", "UCS-4BE", "UCS-4LE", "UCS-2", "UCS-2BE", "UCS-2LE", "UTF-32", "UTF-32BE", "UTF-32LE", "UTF-16", "UTF-16BE", "UTF-16LE", "UTF-8", "UTF-7", "UTF7-IMAP", "ASCII", "EUC-JP", "SJIS", "eucJP-win", "SJIS-win", "JIS", "ISO-2022-JP", "Windows-1252", "ISO-8859-1", "ISO-8859-2", "ISO-8859-3", "ISO-8859-4", "ISO-8859-5", "ISO-8859-6", "ISO-8859-7", "ISO-8859-8", "ISO-8859-9", "ISO-8859-10", "ISO-8859-13", "ISO-8859-14", "ISO-8859-15", "EUC-CN", "CP936", "HZ", "EUC-TW", "BIG-5", "EUC-KR", "UHC", "ISO-2022-KR", "Windows-1251", "CP866", "KOI8-R" );
		return $list_encoding;

	}

}
?>