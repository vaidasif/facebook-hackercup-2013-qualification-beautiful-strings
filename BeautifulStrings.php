<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vaidis
 * Date: 13.1.28
 * Time: 19.12
 */

$sInputFile = __DIR__.DIRECTORY_SEPARATOR.'beautiful_stringstxt.txt';
$sOutputFile = __DIR__.DIRECTORY_SEPARATOR.'output.txt';
$oBS = new BeautifulStrings();
try {
    $oBS->calculateBeauty($sInputFile, $sOutputFile);
} catch (Exception $e) {
    echo 'Something went wrong at line '.$e->getLine().' : '.$e->getMessage();
    return;
}
echo "Strings beauty's are written to file: ".$sOutputFile;
return;



class BeautifulStrings
{
    const MIN_CHAR_BEAUTY = 1;
    const MAX_CHAR_BEAUTY = 26;

    /**
     * @return array
     */
    private function generateBeautyArray()
    {
        $aRes = array();
        for($i=self::MIN_CHAR_BEAUTY; $i<=self::MAX_CHAR_BEAUTY; $i++) {
            $aRes[] = $i;
        }

        return $aRes;
    }

    /**
     * @param string $sString
     * @return mixed
     */
    private function clearString($sString)
    {
        return preg_replace('/[^a-z]/', '', strtolower(trim($sString)));
    }

    /**
     * @param $hFileHandle
     * @param int $iLineCount
     * @param int $iLineBeauty
     * @throws Exception
     */
    private function writeLineToFile($hFileHandle, $iLineCount, $iLineBeauty = 0)
    {
        if (fwrite($hFileHandle, "Case #$iLineCount: $iLineBeauty".PHP_EOL)===false) {
            throw new Exception('Could not write to file.');
        }
    }

    /**
     * @param string $sString
     * @param array $aCharsBeauty
     * @return int
     */
    private function getStringBeauty($sString, $aCharsBeauty)
    {
        $iStringBeauty = 0;

        //count chars in string
        $aCharsCount = count_chars($sString, 1);

        //sort array descending by values
        asort($aCharsCount);

        while ($aCharsCount) {
            $iCharCount = (int)array_pop($aCharsCount);
            $iCharBeauty = (int)array_pop($aCharsBeauty);

            $iStringBeauty += ($iCharCount*$iCharBeauty);
        }
        return $iStringBeauty;
    }

    /**
     * @param string $sInputFile
     * @param string $sOutputFile
     * @throws Exception
     */
    public function calculateBeauty($sInputFile, $sOutputFile)
    {
        if (!file_exists($sInputFile)) {
            throw new Exception('Input file not exists.');
        }

        $aAvailableCharsBeauties = $this->generateBeautyArray();

        $hInputFile = fopen($sInputFile, "r");
        $hOutputFile = fopen($sOutputFile, "w");

        $iMaxStrings = 0;
        $iCurrentString = 1;
        while (!feof($hInputFile)) {
            $sLine = fgets($hInputFile);

            //get max lines count - first line
            if (!$iMaxStrings) {
                $iMaxStrings = (int)$sLine;
                continue;
            }

            //if where are lines more than defined - skip them
            if ($iCurrentString > $iMaxStrings) {
                continue;
            }

            //get string beauty
            $iStringBeauty = $this->getStringBeauty($this->clearString($sLine), $aAvailableCharsBeauties);

            //write calculated beauty to file
            $this->writeLineToFile($hOutputFile, $iCurrentString, $iStringBeauty);

            //increase current line counter
            $iCurrentString++;
        }
        //close input file handle
        fclose($hInputFile);

        //adding missing lines, if lines count defined is bigger than actual lines count
        while ($iCurrentString<=$iMaxStrings) {
            $this->writeLineToFile($hOutputFile, $iCurrentString);
            $iCurrentString++;
        }

        //close output file handle
        fclose($hOutputFile);
    }
}
