<?php
// global variables used for correct behavior of parser
$presentHeader = false;
$instructionNumber = 1;
$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// next 4 functions checks validity of given shit by regex 
function isValidVar ($item)
{
  if (preg_match("/^(GF|LF|TF)@[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*/", $item))
    return true;
  return false;
}
function isValidLabel ($item)
{
  if (preg_match("/^[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*/", $item))
    return true;
  return false;
}
function isValidSymb ($item)
{
  if (preg_match("/^(GF|LF|TF|string|int|bool|nil)@[a-zA-Z_$&%*!?-\][a-zA-Z0-9\_$&%*!?-@]*/", $item))
    return true;
  return false;
}
function isValidType ($item)
{
  if (preg_match("/^(string|int|bool)$/", $item))
    return true;
  return false;
}

// 2 functions for adding xml <instruction> & </instruction> tags to $output
function addInstructionStart ($name)
{
  global $output, $instructionNumber;
  $output .= "\t<instruction order=\"";
  $output .= $instructionNumber++;
  $output .= "\" opcode=\"";
  $output .= strtoupper($name)."\">\n";
}
function addInstructionEnd ()
{
  global $output;
  $output .= "\t</instruction>\n";
}

function genInstructionNoArg ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);
  addInstructionEnd();
}
function genInstructionVar ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  // fix with comment present 
  if (isValidVar($data[1]))
    $output .= "\t\t<arg1 type=\"var\">".$data[1]."</arg1>\n";
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionLabel ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  // fix with comment present 
  if (isValidLabel($data[1]))
    $output .= "\t\t<arg1 type=\"label\">".$data[1]."</arg1>\n";
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionSymb ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  // fix with comment present 
  if (isValidSymb($data[1]))
  {
    // check whether variable or constant
    if (preg_match("/^(GF|LF|TF)/", $data[1]))
      $output .= "\t\t<arg1 type=\"var\">".$data[1]."</arg1>\n";
    else
    {
      $strippedType = substr($data[1], 0, strpos($data[1], '@'));
      $strippedValue = substr($data[1], strpos($data[1], '@') + 1);
      $output .= "\t\t<arg1 type=\"$strippedType\">".$strippedValue."</arg1>\n";
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarSymb ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    $output .= "\t\t<arg1 type=\"var\">".$data[1]."</arg1>\n";
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      $output .= "\t\t<arg2 type=\"var\">".$data[2]."</arg2>\n";
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      $output .= "\t\t<arg2 type=\"$strippedType\">".$strippedValue."</arg2>\n";
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarType ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    $output .= "\t\t<arg1 type=\"var\">".$data[1]."</arg1>\n";
  else
    exit(23);

  if (isValidType($data[2]))
    $output .= "\t\t<arg2 type=\"$data[2]\"></arg2>\n";
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionLabelDoubleSymb ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  // fix with comment present 
  if (isValidLabel($data[1]))
    $output .= "\t\t<arg1 type=\"label\">".$data[1]."</arg1>\n";
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      $output .= "\t\t<arg2 type=\"var\">".$data[2]."</arg2>\n";
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      $output .= "\t\t<arg2 type=\"$strippedType\">".$strippedValue."</arg2>\n";
    }
  }
  else
    exit(23);

  if (isValidSymb($data[3]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[3]))
      $output .= "\t\t<arg3 type=\"var\">".$data[3]."</arg3>\n";
    else
    {
      $strippedType = substr($data[3], 0, strpos($data[3], '@'));
      $strippedValue = substr($data[3], strpos($data[3], '@') + 1);
      $output .= "\t\t<arg3 type=\"$strippedType\">".$strippedValue."</arg3>\n";
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarDoubleSymb ($data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  // fix with comment present 
  if (isValidVar($data[1]))
    $output .= "\t\t<arg1 type=\"var\">".$data[1]."</arg1>\n";
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      $output .= "\t\t<arg2 type=\"var\">".$data[2]."</arg2>\n";
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      $output .= "\t\t<arg2 type=\"$strippedType\">".$strippedValue."</arg2>\n";
    }
  }
  else
    exit(23);

  if (isValidSymb($data[3]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[3]))
      $output .= "\t\t<arg3 type=\"var\">".$data[3]."</arg3>\n";
    else
    {
      $strippedType = substr($data[3], 0, strpos($data[3], '@'));
      $strippedValue = substr($data[3], strpos($data[3], '@') + 1);
      $output .= "\t\t<arg3 type=\"$strippedType\">".$strippedValue."</arg3>\n";
    }
  }
  else
    exit(23);

  addInstructionEnd();
}

function parseLines ($line)
{
  if ($line[0][0] == '#') return;
  switch (strtoupper($line[0]))
  {
    // NO ARGUMENTS - 5 instructions
    case 'CREATEFRAME':
    case 'PUSHFRAME':
    case 'POPFRAME':
    case 'RETURN':
    case 'BREAK':
      genInstructionNoArg($line);
      break;

    // <var> argument
    case 'DEFVAR':
    case 'POPS':
      genInstructionVar($line);
      break;

    // <label> argument
    case 'LABEL':
    case 'JUMP':
    case 'CALL':
      genInstructionLabel($line);
      break;

    // <symb> argument
    case 'PUSHS':
    case 'WRITE':
    case 'EXIT':
    case 'DPRINT':
      genInstructionSymb($line);
      break;

    // <var> <symb> arguments
    case 'MOVE':
    case 'NOT':
    case 'INT2CHAR':
    case 'STRLEN':
    case 'TYPE':
      genInstructionVarSymb($line);
      break;
    
    // <var> <type> arguments
    case 'READ':
      genInstructionVarType($line);
      break;
    
    // <label> <symb1> <symb2> arguments
    case 'JUMPIFEQ':
    case 'JUMPIFNEQ':
      genInstructionLabelDoubleSymb($line);
      break;

    // <var> <symb1> <symb2> arguments 
    case 'ADD':
    case 'SUB':
    case 'MUL':
    case 'IDIV':
    case 'LT':
    case 'GT':
    case 'EQ':
    case 'AND':
    case 'OR':
    case 'STRI2INT':
    case 'CONCAT':
    case 'GETCHAR':
    case 'SETCHAR':
      genInstructionVarDoubleSymb($line);
      break;

    default:
      echo("Yo, nigga, you fucked up hard. That's not supported! Exiting...");
      exit(22);
  }
}

// for correct displaying error msgs to stderr, taken from project assignment
ini_set('display_errors', 'stderr');

// check whether --help option is present, if so print help message and exit
if ($argc == 2)
{
  if ($argv[1] == '--help')
  {
    echo("Usage: parser.php [options] <inputFile\n\n");
    echo("\t[options]:\n");
    echo("\t\t --help - shows this info, if this option is present rest is ignored\n");
    exit(0);
  }
  else
  {
    echo("Wrong option passed, run `parser.php --help` for further info, exiting...\n");
    exit(1);
  }
}
else if ($argc > 2)
{
  echo("Wrong number of passed options, run `parser.php --help` for further info, exiting...\n");
  exit(1);
}

// read line by line from stdin
while ($line = fgets(STDIN))
{
  // trim $line from newLine character and explode it by white space
  $splittedLine = explode(' ', trim($line, "\n"));

  // check if correct header is present and decide action depending on it
  if ($presentHeader == false)
  {
    // maybe add fix for comment
    if (preg_match("/^.ippcode21$/i", $splittedLine[0])) $presentHeader = true;
    else
    {
      echo("Missing or wrong header, expected '.IPPcode21', exiting...\n");
      exit(21);
    }
    $output .= "<program language=\"IPPcode21\">\n";
    continue;
  }

  // parse rest of file
  parseLines($splittedLine);
}





$output .= "</program>\n";
echo $output;
?>