import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import matplotlib.pyplot as plt

import mysql.connector

mydb = mysql.connector.connect(
  host="*******",
  user="*******",
  password="*******",
  database="*******"
)

mycursor = mydb.cursor()
mycursor.execute("SELECT * FROM sensordata ORDER BY time_stamp DESC LIMIT 1")
myresult = mycursor.fetchone()

moistureDB = float(myresult[1])
tdsDB = float(myresult[2])
pHDB = float(myresult[3])
ecDB = float(myresult[4])
lightDB = float(myresult[5])
tempDB = float(myresult[6])
humidDB = float(myresult[7])
waterLevelDB = float(myresult[8])
# Create Antecedents and Consequent variables ----------------------------

# Input Variables --------------------------------------------------------

#moisture
tds = ctrl.Antecedent(np.arange(0, 1500, 100), 'TDS')
pH = ctrl.Antecedent(np.arange(0, 14, 0.1), 'pH')
ec = ctrl.Antecedent(np.arange(0, 2.6, 0.2), 'EC')
#ambient_light
temperature = ctrl.Antecedent(np.arange(0, 50, 10), 'Temperature')
humidity = ctrl.Antecedent(np.arange(0, 100, 10), 'Humditiy')
#waterLevel

# Output Variables --------------------------------------------------------

#Relay 1
snapAnB = ctrl.Consequent(np.arange(0, 5000, 100), 'snapAnB')
#Relay 2
pHUp = ctrl.Consequent(np.arange(0, 5000, 100), 'pHUp')
#Relay 3
pHDown = ctrl.Consequent(np.arange(0, 5000, 100), 'pHDown') 
#Relay 4
waterPumptoTube = ctrl.Consequent(np.arange(0, 30000, 500), 'waterPumptoTube') 
#Relay 5
waterPumptoReservoir = ctrl.Consequent(np.arange(0, 5000, 100), 'waterPumptoReservoir')
#Relay 6
mixingPump = ctrl.Consequent(np.arange(0, 10000, 100), 'mixingPump') 
#Relay 7
fans = ctrl.Consequent(np.arange(0, 60000, 6000), 'fans') 
#Relay 8
growLight = ctrl.Consequent(np.arange(0, 1, 0.1), 'growLight') 
#Relay 8
growLight = ctrl.Consequent(np.arange(0, 1, 0.1), 'growLight') 

# Define fuzzy sets for parameters ----------------------------------------

#moisture

tds['low'] = fuzz.trapmf(tds.universe,    [0, 0, 400, 600])
tds['medium'] = fuzz.trapmf(tds.universe, [400, 600, 800, 1000])
tds['high'] = fuzz.trapmf(tds.universe,   [800, 1000, 1500, 1500])

pH['acidic'] = fuzz.trapmf(pH.universe,   [0, 0, 4, 6])
pH['neutral'] = fuzz.trapmf(pH.universe,  [5, 6, 7, 8])
pH['alkaline'] = fuzz.trapmf(pH.universe, [7, 9, 14, 14])

ec['low'] = fuzz.trapmf(ec.universe,    [0, 0, 0.8, 1])
ec['optimal'] = fuzz.trapmf(ec.universe, [0.8, 1, 1.6, 1.8])
ec['high'] = fuzz.trapmf(ec.universe,   [1.6, 1.8, 2.6, 2.6])

#ambient_light

temperature['cold'] = fuzz.trapmf(temperature.universe,   [0, 0, 10, 18])
temperature['normal'] = fuzz.trapmf(temperature.universe, [15, 18, 25, 30])
temperature['hot'] = fuzz.trapmf(temperature.universe,   [25, 30, 50, 50])

humidity['dry'] = fuzz.trapmf(humidity.universe,   [0, 0, 50, 60])
humidity['moist'] = fuzz.trapmf(humidity.universe, [50, 60, 80, 90])
humidity['wet'] = fuzz.trapmf(humidity.universe,   [80, 90, 100, 100])

#waterLevel

# Define fuzzy sets for output ----------------------------------------

snapAnB['off'] = fuzz.trimf(snapAnB.universe, [0, 0, 0])
snapAnB['short'] = fuzz.trapmf(snapAnB.universe, [0, 0, 1500, 2000])
snapAnB['medium'] = fuzz.trapmf(snapAnB.universe, [1500, 2000, 3000, 3500])
snapAnB['long'] = fuzz.trapmf(snapAnB.universe, [3000, 3500, 5000, 5000])

pHUp['off'] = fuzz.trimf(pHUp.universe, [0, 0, 0])
pHUp['short'] = fuzz.trapmf(pHUp.universe, [0, 0, 1500, 2000])
pHUp['medium'] = fuzz.trapmf(pHUp.universe, [1500, 2000, 3000, 3500])
pHUp['long'] = fuzz.trapmf(pHUp.universe, [3000, 3500, 5000, 5000])

pHDown['off'] = fuzz.trimf(pHDown.universe, [0, 0, 0])
pHDown['short'] = fuzz.trapmf(pHDown.universe, [0, 0, 1500, 2000])
pHDown['medium'] = fuzz.trapmf(pHDown.universe, [1500, 2000, 3000, 3500])
pHDown['long'] = fuzz.trapmf(pHDown.universe, [3000, 3500, 5000, 5000])

waterPumptoTube['off'] = fuzz.trimf(waterPumptoTube.universe, [0, 0, 0])
waterPumptoTube['on'] = fuzz.trapmf(waterPumptoTube.universe, [0, 30000, 30000, 30000])

waterPumptoReservoir['off'] = fuzz.trimf(waterPumptoReservoir.universe, [0, 0, 0])
waterPumptoReservoir['short'] = fuzz.trapmf(waterPumptoReservoir.universe, [0, 0, 1500, 2000])
waterPumptoReservoir['medium'] = fuzz.trapmf(waterPumptoReservoir.universe, [1500, 2000, 3000, 3500])
waterPumptoReservoir['long'] = fuzz.trapmf(waterPumptoReservoir.universe, [3000, 3500, 5000, 5000])

mixingPump['off'] = fuzz.trimf(mixingPump.universe, [0, 0, 0])
mixingPump['short'] = fuzz.trapmf(mixingPump.universe, [0, 0, 2000, 4000])
mixingPump['medium'] = fuzz.trapmf(mixingPump.universe, [2000, 4000, 6000, 8000])
mixingPump['long'] = fuzz.trapmf(mixingPump.universe, [6000, 8000, 10000, 10000])

fans['off'] = fuzz.trapmf(fans.universe, [0, 0, 0, 0])
fans['short'] = fuzz.trapmf(fans.universe, [0, 0, 12000, 18000])
fans['medium'] = fuzz.trapmf(fans.universe, [12000, 18000, 30000, 36000])
fans['long'] = fuzz.trapmf(fans.universe, [30000, 36000, 60000, 60000])

# Define fuzzy rules for control ----------------------------------------

tdsECRule1 = ctrl.Rule(ec['high'] & tds['high'], (snapAnB['off'], waterPumptoTube['off'], waterPumptoReservoir['long'], mixingPump['medium']))
tdsECRule2 = ctrl.Rule(ec['high'] & tds['medium'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['short'], mixingPump['medium']))
tdsECRule3 = ctrl.Rule(ec['high'] & tds['low'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['short'], mixingPump['medium']))
tdsECRule4 = ctrl.Rule(ec['optimal'] & tds['high'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['short'], mixingPump['medium']))
tdsECRule5 = ctrl.Rule(ec['optimal'] & tds['medium'], (snapAnB['off'], waterPumptoTube['on'], waterPumptoReservoir['off'], mixingPump['off']))
tdsECRule6 = ctrl.Rule(ec['optimal'] & tds['low'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['off'], mixingPump['medium']))
tdsECRule7 = ctrl.Rule(ec['low'] & tds['high'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['short'], mixingPump['medium']))
tdsECRule8 = ctrl.Rule(ec['low'] & tds['medium'], (snapAnB['short'], waterPumptoTube['off'], waterPumptoReservoir['off'], mixingPump['medium']))
tdsECRule9 = ctrl.Rule(ec['low'] & tds['low'], (snapAnB['long'], waterPumptoTube['off'], waterPumptoReservoir['off'], mixingPump['medium']))

pHRule1 = ctrl.Rule(pH['acidic'], (pHUp['long'], pHDown['off']))
pHRule2 = ctrl.Rule(pH['neutral'], (pHUp['off'], pHDown['off']))
pHRule3 = ctrl.Rule(pH['alkaline'], (pHUp['off'], pHDown['long']))

thRule1 = ctrl.Rule(temperature['hot'] & humidity['dry'], fans['short'])
thRule2 = ctrl.Rule(temperature['normal'] & humidity['dry'], fans['short'])
thRule3 = ctrl.Rule(temperature['cold'] & humidity['dry'], fans['short'])
thRule4 = ctrl.Rule(temperature['hot'] & humidity['moist'], fans['medium'])
thRule5 = ctrl.Rule(temperature['normal'] & humidity['moist'], fans['off'])
thRule6 = ctrl.Rule(temperature['cold'] & humidity['moist'], fans['medium'])
thRule7 = ctrl.Rule(temperature['hot'] & humidity['wet'], fans['long'])
thRule8 = ctrl.Rule(temperature['normal'] & humidity['wet'], fans['long'])
thRule9 = ctrl.Rule(temperature['cold'] & humidity['wet'], fans['long'])

# Create fuzzy control system -------------------------------------------

tdsEC_ctrl = ctrl.ControlSystem([tdsECRule1, tdsECRule2, tdsECRule3, tdsECRule4, tdsECRule5, tdsECRule6, tdsECRule7, tdsECRule8, tdsECRule9])
tdsECmotorDuration = ctrl.ControlSystemSimulation(tdsEC_ctrl)

pH_ctrl = ctrl.ControlSystem([pHRule1, pHRule2, pHRule3])
pHUpDownDuration = ctrl.ControlSystemSimulation(pH_ctrl)

fan_ctrl = ctrl.ControlSystem([thRule1, thRule2, thRule3, thRule4, thRule5, thRule6, thRule7, thRule8, thRule9])
fanDuration = ctrl.ControlSystemSimulation(fan_ctrl)

# Simulate sensor inputs (replace these with actual sensor readings)-----

print("TDS: ", tdsDB)
print("EC: ", ecDB)
tdsECmotorDuration.input['TDS'] = tdsDB
tdsECmotorDuration.input['EC'] = ecDB
tdsECmotorDuration.compute()

print("pH: ", pHDB)
pHUpDownDuration.input['pH'] = pHDB
pHUpDownDuration.compute()

print("Temp: ", tempDB)
print("Humid: ", humidDB)
fanDuration.input['Temperature'] = tempDB
fanDuration.input['Humditiy'] = humidDB
fanDuration.compute()

# Output control actions ------------------------------------------------
snapAnBOutput = tdsECmotorDuration.output['snapAnB']
print("Snap A and B duration: ", snapAnBOutput, "miliseconds")

waterPumptoTubeOutput = tdsECmotorDuration.output['waterPumptoTube']
print("Water pump to system duration: ", waterPumptoTubeOutput, "miliseconds")

waterPumptoReservoirOutput = tdsECmotorDuration.output['waterPumptoReservoir']
print("Water pump to mixing reservoir duration: ", waterPumptoReservoirOutput, "miliseconds")

mixingPumpOutput = tdsECmotorDuration.output['mixingPump']
print("Mixing reservoir duration: ", mixingPumpOutput, "miliseconds")

pHUpOutput = pHUpDownDuration.output['pHUp']
print("pH Up valve duration: ", pHUpOutput, "miliseconds")

pHDownOutput = pHUpDownDuration.output['pHDown']
print("pH down valve duration: ", pHDownOutput, "miliseconds")

fanOutput = fanDuration.output['fans']
print("Fan duration: ", fanOutput, "miliseconds")