<?php
// for correct displaying error msgs to stderr
ini_set('display_errors', 'stderr');

// check whether --help option is present, if so print help message and exit
if ($argc > 1 && $argv[1] == '--help')
{
  echo("Usage: parser.php [options] <inputFile\n\n");
  echo("\t[options]:\n");
  echo("\t\t --help - shows this info, if this option is present rest is ignored\n");
  echo("\tinputFile - should be of type file.IPPcode21, other file extensions are not supported\n");
  exit(0);
}

// global variables used for correct behavior of parser
$presentHeader = false;
$instructionNumber = 1;
$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// read line by line from stdin
while ($line = fgets(STDIN))
{
  // trim $line from newLine character and explode it by white space
  $splittedLine = explode(' ', trim($line, "\n"));

  // check if correct header is present and decide action depending on it
  if ($presentHeader == false)
  {
    if ($splittedLine[0] == '.IPPcode21') $presentHeader = true;
    else
    {
      echo("Missing or wrong written header, expected '.IPPcode21', exiting...\n");
      exit(21);
    }
    $output .= "<program language=\"IPPcode21\">\n";
  }
}





$output .= "</program>\n";
echo $output;
?>