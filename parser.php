<?php
// check whether --help option is present, if so print help message and exit
if ($argc > 1 && $argv[1] == '--help')
{
  echo("Usage: parser.php [options] <inputFile\n\n");
  echo("\t[options]:\n");
  echo("\t\t --help - shows this info, if this option is present rest is ignored\n");
  echo("\tinputFile - should be of type file.IPPcode21, other file extensions are not supported\n");
  exit(0);
}

$presentHeader = false;
// read line by line from stdin
while ($line = fgets(STDIN))
{
  $splittedLine = explode(' ', trim($line, "\n"));
  if ($presentHeader == false)
  {
    if ($splittedLine[0] == '.IPPcode21') $presentHeader = true;
    else
    {
      echo("Missing or wrong written header, expected '.IPPcode21', exiting...\n");
      exit(21);
    }
  }
}
?>