import argparse
from sys import stderr
import re
import xml.etree.ElementTree as ET

####### GLOBAL VARIABLES
sourceFile = ""
inputFile = ""
instructions = list()
positionInProgram = 0
GF = dict()
labels = dict()
###############################################################################

####### CLASS DEFINITIONS
class Argument:
  # class construtor
  def __init__(self, argType, value):
    self.type = argType
    self.value = value

class Instruction:
  # class constructor
  def __init__(self, name, number):
    self.name = name
    self.number = number
    self.args = []
  
  def addArgument(self, argType, value):
    self.args.append(Argument(argType, value))

class Variable:
  def __init__(self, varType, value):
    self.type = varType
    self.value = value
###############################################################################

####### FUNCTION DEFINITIONS
def checkArgCount(expected, real):
  if expected != real:
    stderr.write("Invalid number of arguments for given instruction, exiting...\n")
    exit(32)

def checkInstruction(inst):
  if (
    inst.name == "CREATEFRAME" or
    inst.name == "PUSHFRAME" or
    inst.name == "POPFRAME" or
    inst.name == "RETURN" or
    inst.name == "BREAK"):
    checkArgCount(0, len(inst.args))
  elif (
    inst.name == "DEFVAR" or
    inst.name == "POPS"):
    checkArgCount(1, len(inst.args))
    checkInstructionVar(inst)
  elif (
    inst.name == "LABEL" or
    inst.name == "JUMP" or
    inst.name == "CALL"):
    checkArgCount(1, len(inst.args))
    checkInstructionLabel(inst)
  elif (
    inst.name == "PUSHS" or
    inst.name == "WRITE" or
    inst.name == "EXIT" or
    inst.name == "DPRINT"):
    checkArgCount(1, len(inst.args))
    checkInstructionSymb(inst)
  elif (
    inst.name == "MOVE" or
    inst.name == "NOT" or
    inst.name == "INT2CHAR" or
    inst.name == "STRLEN" or
    inst.name == "TYPE"):
    checkArgCount(2, len(inst.args))
    checkInstructionVarSymb
  elif (inst.name == "READ"):
    checkArgCount(2, len(inst.args))
    checkInstructionVarType(inst)
  elif (
    inst.name == "JUMPIFEQ" or
    inst.name == "JUMPIFNEQ"):
    checkArgCount(3, len(inst.args))
    checkInstructionLabelDoubleSymb(inst)
  elif (
    inst.name == "ADD" or
    inst.name == "SUB" or
    inst.name == "MUL" or
    inst.name == "IDIV" or
    inst.name == "LT" or
    inst.name == "GT" or
    inst.name == "EQ" or
    inst.name == "AND" or
    inst.name == "OR" or
    inst.name == "STRI2INT" or
    inst.name == "CONCAT" or
    inst.name == "GETCHAR" or
    inst.name == "SETCHAR"):
    checkArgCount(3, len(inst.args))
    checkInstructionVarDoubleSymb(inst)
  else:
    stderr.write("Invalid instruction passed, exiting...\n")
    exit(32)

## checks validity of arguments
def isValidVar(item):
  if not(re.match(r"^(GF|LF|TF)@[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*$", item.value)):
    stderr.write("Argument is not valid var, exiting...\n")
    exit(32)
def isValidLabel(item):
  if not(re.match(r"^[a-zA-Z_$&%*!?-][a-zA-Z0-9_$&%*!?-]*$", item.value)):
    stderr.write("Argument is not valid label, exiting...\n")
    exit(32)
def isValidSymb(item):
  if item.type == "var":
    isValidVar(item)
  else:
    checkValueForType(item)
def isValidType(item):
  if not((re.match(r"^(string|int|bool)$", item.value))):
    stderr.write("Argument is not valid type, exiting...\n")
    exit(32)
def checkValueForType(item):
  if item.type == "nil":
    if item.value != "nil":
      stderr.write("Invalid value for nil type, exiting...\n")
      exit(32)
  elif item.type == "bool":
    if not(item.value == "true" or item.value == "false"):
      stderr.write("Invalid value for bool type, exiting...\n")
      exit(32)
  elif item.type == "string":
    if re.match(r"(\\\\[^0-9])|(\\\\[0-9][^0-9])|(\\\\[0-9][0-9][^0-9])|(\\\\$)", item.value):
      stderr.write("Invalid number of digits for escaped character, exiting...\n")
      exit(32)
  elif item.type == "int":
    if re.search(r"[^\d-]", item.value):
      stderr.write("Invalid value for int type, exiting...")
      exit(32)
## end checks validity of arguments

## checks instruction arguments
def checkInstructionVar(inst):
  if inst.args[0].type != "var":
    stderr.write("Invalid arg type, var expected, exiting...\n")
    exit(32)
  isValidVar(inst.args[0])
def checkInstructionLabel(inst):
  if inst.args[0].type != "label":
    stderr.write("Invalid arg type, label expected, exiting...\n")
    exit(32)
  isValidLabel(inst.args[0])
def checkInstructionSymb(inst):
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[0].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  isValidSymb(inst.args[0])
def checkInstructionVarSymb(inst):
  if inst.args[0].type != "var":
    stderr.write("Invalid arg type, var expected, exiting...\n")
    exit(32)
  isValidVar(inst.args[0])
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[0].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  isValidSymb(inst.args[1])
def checkInstructionVarType(inst):
  if inst.args[0].type != "var":
    stderr.write("Invalid arg type, var expected, exiting...\n")
    exit(32)
  isValidVar(inst.args[0])
  if not(re.match(r"^(string|bool|int)$", inst.args[1].type)):
    stderr.write("Invalid arg type, string, bool or int expected, exiting...\n")
    exit(32)
  isValidType(inst.args[1])
def checkInstructionLabelDoubleSymb(inst):
  if inst.args[0].type != "label":
    stderr.write("Invalid arg type, label expected, exiting...\n")
    exit(32)
  if isValidLabel(inst.args[0]):
    print("Valid label")
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[1].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  if isValidSymb(inst.args[1]):
    print("Valid symb")
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[2].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  if isValidSymb(inst.args[2]):
    print("Valid symb")
def checkInstructionVarDoubleSymb(inst):
  if inst.args[0].type != "var":
    stderr.write("Invalid arg type, var expected, exiting...\n")
    exit(32)
  if isValidVar(inst.args[0]):
    print("Valid var")
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[1].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  if isValidSymb(inst.args[1]):
    print("Valid symb")
  if not(re.match(r"^(var|string|bool|int|nil)$", inst.args[1].type)):
    stderr.write("Invalid arg type, exiting...\n")
    exit(32)
  if isValidSymb(inst.args[2]):
    print("Valid symb")
## end checks instruction arguments

def getVariable(frame, name):
  # TODO: implement other frames
  if frame == "GF":
    if not name in GF.keys():
      stderr.write("Non existing variable, exiting...")
      exit(54)
    return GF[name]
def checkVarExistence(frame, name):
  # TODO: implement other frames
  if frame == "GF":
    if not(name in GF.keys()):
      stderr.write("Non existing variable, exiting...\n")
      exit(54)
  else:
    stderr.write("Not yet implemented, exiting...\n")
    exit(99)
def saveToVariable(frame, name, arg):
  # TODO: implement other frames and loading from other variables
  if re.match(r"(int|bool|string)", arg.type):
    if frame == "GF":
      GF[name] = Variable(arg.type, arg.value)
    else:
      stderr.write("Saving to other frames not implemented\n")
      exit(99)
  elif arg.type == var:
    print("Louading from other variables not supported\n")
    exit(99)
  else:
    stderr.write("Unexpected error when saving to variable, exiting...\n")
    exit(99)


def iDEFVAR(var):
  # TODO: implement other frames
  splitted = var.value.split("@")    
  tmp = Variable(None, None)
  if splitted[0] == "GF":
    if splitted[1] in GF.keys():
      stderr.write("Variable already exists, exiting...\n")
      exit(52)
    GF.update({splitted[1]: tmp})
def iMOVE(var, symb):
  splittedVar = var.value.split("@")
  checkVarExistence(splittedVar[0], splittedVar[1])
  saveToVariable(splittedVar[0], splittedVar[1], symb)
def iJUMPIF(instName, labelName, var1, var2):
  global positionInProgram
  if var1.type == "var":
    tmp = var1.value.split("@")
    checkVarExistence(tmp[0], tmp[1])
    var1 = getVariable(tmp[0], tmp[1])
  if var2.type == "var":
    tmp = var2.value.split("@")
    checkVarExistence(tmp[0], tmp[1])
    var2 = getVariable(tmp[0], tmp[1])

  if (var1.type != var2.type):
    if (var1.type == "nil" or var2.type == "nil"):
      return
    stderr.write("Variables are of different type, exiting...\n")
    exit(53)

  if instName == "JUMPIFEQ":
    if (var1.value == var2.value):
      if not(labelName in labels.keys()):
        stderr.write("Non existing label, exiting...\n")
        exit(52)
      positionInProgram = int(labels[labelName]-1)
  elif instName == "JUMPIFNEQ":
    if (var1.value != var2.value):
      if not(labelName in labels.keys()):
        stderr.write("Non existing label, exiting...\n")
        exit(52)
      positionInProgram = int(labels[labelName]-1)
def iWRITE(var):
  if var.type == "nil":
    print("", end='')
  elif var.type == "bool":
    if var.value == "true":
      print("true", end='')
    else:
      print("false", end='')
  elif var.type == "var":
    tmp = var.value.split("@")
    var = getVariable(tmp[0], tmp[1])
    if var.value == None:
      print("", end='')
    else:
      print(var.value, end='')
  else:
    # TODO: fix escape sequences in string
    print(var.value, end='')
def iCONCAT(var1, var2, var3):
  if var2.type == "var":
    tmp = var2.value.split("@")
    var2 = getVariable(tmp[0], tmp[1])
  elif var2.type != "string":
    stderr.write("Invalid type for CONCAT, only string is supported, exiting...\n")
    exit(53)

  if var3.type == "var":
    tmp = var3.value.split("@")
    var3 = getVariable(tmp[0], tmp[1])
  elif var3.type != "string":
    stderr.write("Invalid type for CONCAT, only string is supported, exiting...\n")
    exit(53)
  
  # check existence of variable for saving
  varToSave = var1.value.split("@")
  getVariable(varToSave[0], varToSave[1])
  
  if var2.value == None:
    var2.value = ""
  if var3.value == None:
    var3.value = ""
  
  newVal = Argument("string", var2.value+var3.value)
  saveToVariable(varToSave[0], varToSave[1], newVal)


def interpretInstruction(inst):
  global positionInProgram
  #defVar not complete
  if inst.name == "MOVE":
    var = inst.args[0]
    symb = inst.args[1]
    iMOVE(var, symb)
  elif inst.name == "DEFVAR":
    iDEFVAR(inst.args[0])
  elif inst.name == "WRITE":
    iWRITE(inst.args[0])
  elif inst.name == "CONCAT":
    var1 = inst.args[0]
    var2 = inst.args[1]
    var3 = inst.args[2]
    iCONCAT(var1, var2, var3)
  elif inst.name == "LABEL":
    pass
  elif inst.name == "JUMP":
    labelName = inst.args[0].value
    if not(labelName in labels.keys()):
      stderr.write("Label does not exist, exiting...\n")
      exit(52)
    positionInProgram = int(labels[labelName]-1)
  elif inst.name == "JUMPIFEQ":
    label = inst.args[0].value
    var1 = inst.args[1]
    var2 = inst.args[2]
    iJUMPIF(inst.name, label, var1, var2)



      
    

if True:
  ###############################################################################

  ####### ARGUMENT PARSING
  argparser = argparse.ArgumentParser()
  argparser.add_argument("--source", nargs=1, help="Input file with XML representation of code")
  argparser.add_argument("--input", nargs=1, help="File containing inputs for reading of interpreted code")
  args = vars(argparser.parse_args())
  if args['input']:
    inputFile = args['input'][0]
  else:
    inputFile = None
  if args['source']:
    sourceFile = args['source'][0]
  else:
    sourceFile = None
  ###############################################################################

  #print("Value of sourceFile: "+str(sourceFile))
  #print("Value of inputFile: "+str(inputFile))
  #print()

  ###### XML SOURCE FILE PARSING
  tree = None
  if sourceFile:
    try:
      tree = ET.parse(sourceFile)
    except Exception as e:
      stderr.write(str(e)+"\n")
      stderr.write("Error occured when parsing source file, exiting...\n")
      exit(31)
  # add implementation for reading from STDIN
  ###############################################################################

  ###### XML SORTING
  root = tree.getroot()
  if root.tag != 'program':
    stderr.write("Root tag is not program, exiting...\n")
    exit(32)

  #sort <instruction> tags by opcode
  try:
    root[:] = sorted(root, key=lambda child: (child.tag, int(child.get('order'))))
  except Exception as e:
    stderr.write(str(e)+"\n")
    stderr.write("Error occured when sorting <instruction> elements, exiting...\n")
    exit(32)

  # sort <arg#> elements
  for child in root:
    try:
      child[:] = sorted(child, key=lambda child: (child.tag))
    except Exception as e:
      stderr.write(str(e)+"\n")
      stderr.write("Error occured when sorting <arg#> elements, exiting...\n")
      exit(32)
  ###############################################################################

  ###### XML INNER VALIDITY CHECKS
  # <program> check of correct 'language' attribute
  if not('language' in list(root.attrib.keys())):
    stderr.write("Unable to find 'language' attribute for <program> tag, exiting...\n")
    exit(32)
  if not(re.match(r"ippcode21", root.attrib['language'], re.IGNORECASE)):
    stderr.write("Wrong <program> tag 'language' attrib value, exiting...\n")
    exit(32)

  # <instruction> checks of tag and correct attributes
  prevOrder = 0
  for child in root:
    if child.tag != 'instruction':
      stderr.write("First level elements after root should be called <instruction>, exiting...\n")
      exit(32)

    # check correct attributes
    ca = list(child.attrib.keys())
    if not('order' in ca) or not('opcode' in ca):
      stderr.write("<instruction> element has to have 'order' & 'opcode' attributes, exiting...\n")
      exit(32)

    # check that no 2 elemeents with same order number
    if prevOrder == child.attrib['order']:
      stderr.write("2 <instruction> elements with same order found, exiting...\n")
      exit(32)
    prevOrder = child.attrib['order']

  # iterate over <instruction> elements
  for child in root:
    # check that there are not diplicates in child elements
    dup = set()
    for c in child:
      if c.tag not in dup:
        dup.add(c.tag)
    if len(dup) != len(child):
      stderr.write("Found duplicate <arg#> elements, exiting...\n")
      exit(32)

    # <arg#> checks
    for c in child:
      if not(re.match(r"arg[123]", c.tag)):
        stderr.write("Only <arg#> where # ranges from 1-3 are allowed as subelements for <instruction>, exiting...\n")
        exit(32)

      # <arg#> attribute check
      att = list(c.attrib)
      if not('type' in att):
        stderr.write("<arg#> elements has to have 'type' attribute, exiting...\n")
        exit(32)
  ###############################################################################

  ###### FILLING INSTRUCTIONS LIST
  instCount = 1
  for elem in root:
    instructions.append(
      Instruction(elem.attrib['opcode'].upper(), instCount)
    )
    for subelem in elem:
      instructions[instCount-1].addArgument(
        subelem.attrib['type'].lower(), subelem.text
      )
    instCount += 1

  ###############################################################################

  ###### CHECK INSTRUCTIONS
  for i in instructions:
    checkInstruction(i)
  ###############################################################################

###### INTERPRET INSTRUCTIONS
for i in instructions:
  if i.name == "LABEL":
    labels.update({i.args[0].value: i.number})
while positionInProgram != len(instructions):
  #print(instructions[positionInProgram].name, instructions[positionInProgram].number)
  interpretInstruction(instructions[positionInProgram])
  positionInProgram += 1
###############################################################################

print("\nvariables in GF:")
for var in GF:
  print(var, GF[var].type, GF[var].value)
#print("\nlabels:")
#for var in labels:
#  print(var, labels[var])