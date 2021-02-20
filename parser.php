<?php
// for correct displaying error msgs to stderr, taken from project assignment
ini_set('display_errors', 'stderr');

// global variables used for correct behavior of parser
$presentHeader = false;
$instructionNumber = 1;
$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

function stripComment(String $item)
{
  if (strpos($item, '#') != NULL)
    return substr($item, 0, strpos($item, '#'));
  return $item;
}

// next 4 functions checks validity of given shit by regex 
function isValidVar (String $item)
{
  if (preg_match("/^(GF|LF|TF)@[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*/", $item))
    return true;
  return false;
}
function isValidLabel (String $item)
{
  if (preg_match("/^[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*/", $item))
    return true;
  return false;
}
function isValidSymb (String $item)
{
  if (preg_match("/^(GF|LF|TF|string|int|bool|nil)@[a-zA-Z_$&%*!?-\][a-zA-Z0-9\_$&%*!?-@]*/", $item))
    return true;
  return false;
}
function isValidType (String $item)
{
  if (preg_match("/^(string|int|bool)$/", $item))
    return true;
  return false;
}

// 2 functions for adding xml <instruction> & </instruction> tags to $output
function addInstructionStart (String $name)
{
  global $output, $instructionNumber;
  $output .= "\t<instruction order=\"";
  $output .= $instructionNumber++;
  $output .= "\" opcode=\"";
  $output .= "$name\">\n";
}
function addInstructionEnd ()
{
  global $output;
  $output .= "\t</instruction>\n";
}
function addArgument (Int $number, String $type, String $value)
{
  global $output;

  $output .= "\t\t<arg$number type=\"$type\">$value</arg$number>\n";
}

function genInstructionNoArg (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);
  addInstructionEnd();
}
function genInstructionVar (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    addArgument(1, "var", $data[1]);
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionLabel (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidLabel($data[1]))
    addArgument(1, "label", $data[1]);
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionSymb (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidSymb($data[1]))
  {
    // check whether variable or constant
    if (preg_match("/^(GF|LF|TF)/", $data[1]))
      addArgument(1, "var", $data[1]);
    else
    {
      $strippedType = substr($data[1], 0, strpos($data[1], '@'));
      $strippedValue = substr($data[1], strpos($data[1], '@') + 1);
      checkValuesForType($strippedType, $strippedValue);
      addArgument(1, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarSymb (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    addArgument(1, "var", $data[1]);
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      addArgument(2, "var", $data[2]);
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      // check value for specific type
      checkValuesForType($strippedType, $strippedValue);
      addArgument(2, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarType (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    addArgument(1, "var", $data[1]);
  else
    exit(23);

  if (isValidType($data[2]))
    addArgument(2, $data[2], "");
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionLabelDoubleSymb (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidLabel($data[1]))
    addArgument(1, "label", $data[1]);
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      addArgument(2, "var", $data[2]);
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      checkValuesForType($strippedType, $strippedValue);
      addArgument(2, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  if (isValidSymb($data[3]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[3]))
      addArgument(3, "var", $data[3]);
    else
    {
      $strippedType = substr($data[3], 0, strpos($data[3], '@'));
      $strippedValue = substr($data[3], strpos($data[3], '@') + 1);
      checkValuesForType($strippedType, $strippedValue);
      addArgument(3, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  addInstructionEnd();
}
function genInstructionVarDoubleSymb (Array $data)
{
  global $instructionNumber, $output;

  addInstructionStart($data[0]);

  if (isValidVar($data[1]))
    addArgument(1, "var", $data[1]);
  else
    exit(23);

  if (isValidSymb($data[2]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[2]))
      addArgument(2, "var", $data[2]);
    else
    {
      $strippedType = substr($data[2], 0, strpos($data[2], '@'));
      $strippedValue = substr($data[2], strpos($data[2], '@') + 1);
      checkValuesForType($strippedType, $strippedValue);
      addArgument(2, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  if (isValidSymb($data[3]))
  {
    if (preg_match("/^(GF|LF|TF)/", $data[3]))
      addArgument(3, "var", $data[3]);
    else
    {
      $strippedType = substr($data[3], 0, strpos($data[3], '@'));
      $strippedValue = substr($data[3], strpos($data[3], '@') + 1);
      checkValuesForType($strippedType, $strippedValue);
      addArgument(3, $strippedType, $strippedValue);
    }
  }
  else
    exit(23);

  addInstructionEnd();
}

function checkArgumentsCount (Int $expectedCount, Array $data)
{
  if ($expectedCount != ($realCount = count($data) - 1))
  {
    fwrite(
      STDERR,
      "Invalid number of arguments, expected $expectedCount, got $realCount, exiting...\n"
    );
    exit(23);
  }
}
function checkValuesForType (String $type, String $value)
{
  switch ($type)
  {
    case 'nil':
      if ($type == 'nil' && $value != 'nil')
      {
        fwrite(STDERR, "Invalid value for nil type, exiting...\n");
        exit(23);
      }
      break;
    case 'bool':
      if (
        $type == 'bool' &&
        ($value != true || $value != false)
      ){
        fwrite(STDERR, "Invalid value for bool type, exiting...\n");
        exit(23);
      }
      break;
    case 'string':
      if (
        $type == 'string' &&
        preg_match(
          "/(\\\\[^0-9])|(\\\\[0-9][^0-9])|(\\\\[0-9][0-9][^0-9])/",
          $value
        )
      ){
        fwrite(
          STDERR,
          "Invalid number of parameters for escaped character, exiting...\n"
        );
        exit(23);
      }
      break;
    default:
  }
}

function parseLines (Array $lineData)
{
  if ($lineData[0][0] == '#') return;
  switch (strtoupper($lineData[0]))
  {
    // NO ARGUMENTS - 5 instructions
    case 'CREATEFRAME':
    case 'PUSHFRAME':
    case 'POPFRAME':
    case 'RETURN':
    case 'BREAK':
      checkArgumentsCount(0, $lineData);
      genInstructionNoArg($lineData);
      break;

    // <var> argument
    case 'DEFVAR':
    case 'POPS':
      checkArgumentsCount(1, $lineData);
      genInstructionVar($lineData);
      break;

    // <label> argument
    case 'LABEL':
    case 'JUMP':
    case 'CALL':
      checkArgumentsCount(1, $lineData);
      genInstructionLabel($lineData);
      break;

    // <symb> argument
    case 'PUSHS':
    case 'WRITE':
    case 'EXIT':
    case 'DPRINT':
      checkArgumentsCount(1, $lineData);
      genInstructionSymb($lineData);
      break;

    // <var> <symb> arguments
    case 'MOVE':
    case 'NOT':
    case 'INT2CHAR':
    case 'STRLEN':
    case 'TYPE':
      checkArgumentsCount(2, $lineData);
      genInstructionVarSymb($lineData);
      break;
    
    // <var> <type> arguments
    case 'READ':
      checkArgumentsCount(2, $lineData);
      genInstructionVarType($lineData);
      break;
    
    // <label> <symb1> <symb2> arguments
    case 'JUMPIFEQ':
    case 'JUMPIFNEQ':
      checkArgumentsCount(3, $lineData);
      genInstructionLabelDoubleSymb($lineData);
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
      checkArgumentsCount(3, $lineData);
      genInstructionVarDoubleSymb($lineData);
      break;

    default:
      fwrite(
        STDERR,
        "Invalid instruction passed, exiting...\n"
      );
      exit(22);
  }
}

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
    fwrite(
      STDERR,
      "Wrong option passed, run `parser.php --help` for further info, exiting...\n"
    );
    exit(1);
  }
}
else if ($argc > 2)
{
  fwrite(
    STDERR,
    "Wrong number of passed options, run `parser.php --help` for further info, exiting...\n"
  );
  exit(1);
}

// read line by line from stdin
while ($line = fgets(STDIN))
{
  // skip empty lines
  if ($line == "\n") continue;

  /**
   * trim line from newLine character & leading space
   * and then split it by white characters
   */
  $splittedLine = preg_split('/\s+/', stripComment(trim(ltrim($line), "\n")));

  // check if correct header is present and decide action depending on it
  if (!$presentHeader)
  {
    // maybe add fix for comment
    if (preg_match("/^.ippcode19$/i", $splittedLine[0])) $presentHeader = true;
    else
    {
      fwrite(
        STDERR,
        "Missing or wrong header, expected '.IPPcode21', exiting...\n"
      );
      exit(21);
    }
    $output .= "<program language=\"IPPcode21\">\n";
    continue;
  }

  // parse rest of file
  parseLines($splittedLine);
}

// check that file wasn't empty
if (!$presentHeader)
{
  fwrite(STDERR, "Empty file, exiting...\n");
  exit(21);
}

$output .= "</program>\n";
echo $output;
?>