<?php

//New Test
class I18NTestCase extends SeleniumTestCase
{
	function test ()
	{
		$this->open("../../demos/quickstart/index.php?notheme=true&page=Advanced.Samples.I18N.Home&amp;lang=en&amp;notheme=true", "");
		$this->verifyTextPresent("Internationlization in PRADO", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("$12.40", "");
		$this->verifyTextPresent("€100.00", "");
		$this->verifyTextPresent("December 6, 2004", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=zh&amp;notheme=true", "");
		$this->verifyTextPresent("PRADO 国际化", "");
		$this->verifyTextPresent("2004 十二月", "");
		$this->verifyTextPresent("US$ 12.40", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00 ", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=zh_TW&amp;notheme=true", "");
		$this->verifyTextPresent("PRADO 國際化", "");
		$this->verifyTextPresent("2004年12月6日", "");
		$this->verifyTextPresent("US$12.40", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=de&amp;notheme=true", "");
		$this->verifyTextPresent("Internationalisierung in PRADO", "");
		$this->verifyTextPresent("6. Dezember 2004 ", "");
		$this->verifyTextPresent("$ 12,40", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=es&amp;notheme=true", "");
		$this->verifyTextPresent("Internationlization en PRADO", "");
		$this->verifyTextPresent("6 de diciembre de 2004", "");
		$this->verifyTextPresent("US$12.40", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=fr&amp;notheme=true", "");
		$this->verifyTextPresent("Internationalisation avec PRADO", "");
		$this->verifyTextPresent("6 décembre 2004", "");
		$this->verifyTextPresent("12,40 $", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00", "");
		$this->open("../../demos/quickstart/index.php?page=Advanced.Samples.I18N.Home&amp;lang=pl&amp;notheme=true", "");
		$this->verifyTextPresent("Internacjonalizacja w PRADO", "");
		$this->verifyTextPresent("6 grudnia 2004", "");
		$this->verifyTextPresent("US$ 12,40", "");
		$this->verifyTextPresent("46.412,42 €", "");
		$this->verifyTextPresent("€100.00", "");

	}
}

?>