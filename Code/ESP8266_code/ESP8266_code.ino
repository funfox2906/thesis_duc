#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include "HumidityHIH4030.h"
#include <ArduinoJson.h>
#include <Wire.h>
#include "util.h"
//----------------------------------------------

HumidityHIH4030 Humidity;
float humidity;
int boardLed = 16; //D0 GPIO - used for alert if MQTT broker is connected to this board
int greenLed = 2; //D8 GPIO2
int blueLed = 0; // D3 GPIO3
#define PinR  14
#define PinG  12
#define PinB  13
#define SR505 15 //D8 GPIO15
//int redLed = 14 ; //D5 GPIO14
//int blueLed = 12; //D6 GPIO12
//A0 Humidity sensor input pin
const char* ssid = "DucLee";
const char* password = "Duc2906@BME";
const char* mqtt_server = "192.168.1.110";
WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;
char msg[50];
int value = 0;
int detectMotion = 0;
bool c = false;

void setup_wifi() {
  delay(10);
  // We start by connecting to a WiFi network
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  randomSeed(micros());
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  Wire.begin();  
  displayInit();                // initialze OLED display
  displayClear();               // clear the display  
  setTextXY(0,0); 
  displayString("IP:");
  setTextXY(2,0);
  String IP = WiFi.localIP().toString();
  const char* IPAddress = IP.c_str();
  displayString(IPAddress);
}

float getHumidityPercentage(){
  humidity = Humidity.getHumidityPercentage();
  humidity = humidity*(-1);
  return humidity;
}

void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived [");
  Serial.print(topic);
  Serial.print("] ");
  String statuschange = "";
  for (int i = 0; i < length; i++) {
    Serial.print((char)payload[i]);
    statuschange += (char)payload[i];
  }
  Serial.println();
  if (statuschange == "OFF") {
    digitalWrite(greenLed, LOW); 
  } else if (statuschange == "ON"){
    digitalWrite(greenLed, HIGH); 
  } else {
    //RGBLed
    long number = (long) strtol ( &statuschange[1], NULL, 16);
    int r = number >> 16;
    int g = number >> 8 & 0xFF;
    int b = number & 0xFF;
    setRGBColor(r, g, b);
  }
  statuschange = "";
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "ESP8266Client-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str())) {
      Serial.println("connected");
      setTextXY(4,0);
      displayString("MQTT connected");
      client.subscribe("cmnd/MQTTSonoff/ESP_Light");
      client.subscribe("cmnd/MQTTSonoff/RGBColor");
      //client.subscribe("stat/MQTTSonoff/Humidity");
      //client.subscribe("cmnd/MQTTSonoff/POWER1");
    } else {
      Serial.print("fail to connect, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

void setup() {
  pinMode(greenLed, OUTPUT); 
//  pinMode(redLed, OUTPUT); 
  pinMode(blueLed, OUTPUT);  
  pinMode(boardLed, OUTPUT);  
  pinMode(PinR, OUTPUT);
  pinMode(PinG, OUTPUT);
  pinMode(PinB, OUTPUT);
  pinMode(SR505, INPUT);
  analogWriteRange(255);
  analogWriteFreq(1000); 
  Serial.setDebugOutput(true);
  Humidity.begin(A0);
  Serial.begin(115200);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);

              
}

void loop() {
  if (!client.connected()) {
    digitalWrite(boardLed, HIGH); 
    reconnect();
  } else {
    digitalWrite(boardLed, LOW); 
  }
  client.loop();
  long now = millis();
  if (now - lastMsg > 10000) {
    Serial.println("-------------Send Update Messages--------------");
    lastMsg = now;
    ++value;
    snprintf (msg, 75, "%.2f", getHumidityPercentage());
    Serial.print("Humidity: ");
    Serial.println(msg);
    client.publish("stat/MQTTSonoff/Humidity", msg);
    strcpy(msg,""); 
    if (digitalRead(SR505) == HIGH){
      digitalWrite(blueLed, HIGH); 
      snprintf (msg, 75, "%d", 1);
      Serial.print("Publish Motion: ");
      Serial.println(msg);
      client.publish("stat/MQTTSonoff/MotionDetector", msg);
      strcpy(msg,""); 
    } else {
      digitalWrite(blueLed, LOW); 
      snprintf (msg, 75, "%d", 0);
      Serial.print("Motion Detector: ");
      Serial.println(msg);
      client.publish("stat/MQTTSonoff/MotionDetector", msg);
      strcpy(msg,""); 
      }
  }
  delay(1000);
}


//RGB
//int convertHexToRGB
void setRGBColor(int red, int green, int blue)
{
  analogWrite(PinR, red);
  analogWrite(PinG, green);
  analogWrite(PinB, blue);
}
// OLED functions
//void printData(String data)
//{
//  setTextXY(2,0);
//  displayString("");
//
//  setTextXY(2,1);
//  char __data[sizeof(data)];
//  data.toCharArray(__data, sizeof(__data));
//  displayString(__data);
//}

void sendData(unsigned char data)
{
  Wire.beginTransmission(I2C_ADDRESS);  // begin I2C transmission
  Wire.write(CMD_MODE_DATA);            // set mode: data
  Wire.write(data);
  Wire.endTransmission();               // stop I2C transmission
}

void sendCommand(unsigned char command)
{
  Wire.beginTransmission(I2C_ADDRESS);  // begin I2C communication
  Wire.write(CMD_MODE_COMMAND);         // set mode: command
  Wire.write(command);
  Wire.endTransmission();               // End I2C communication
}
void displayInit()
{
  sendCommand(CMD_DISPLAY_OFF); //DISPLAYOFF
  sendCommand(0x8D);            //CHARGEPUMP Charge pump setting
  sendCommand(0x14);            //CHARGEPUMP Charge pump enable
  sendCommand(0xA1);            //SEGREMAP   Mirror screen horizontally (A0)
  sendCommand(0xC8);            //COMSCANDEC Rotate screen vertically (C0)
}

void setTextXY(unsigned char row, unsigned char col)
{
    sendCommand(0xB0 + row);                //set page address
    sendCommand(0x00 + (8*col & 0x0F));     //set column lower address
    sendCommand(0x10 + ((8*col>>4)&0x0F));  //set column higher address
}

void displayString(const char *str)
{
    unsigned char i=0, j=0, c=0;
    while(str[i])
    {
      c = str[i++];
      if(c < 32 || c > 127) //Ignore non-printable ASCII characters. This can be modified for multilingual font.
      {
        c=' '; //Space
      }
      for(j=0;j<8;j++)
      {
         //read bytes from code memory
         sendData(pgm_read_byte(&BasicFont[c-32][j])); //font array starts at 0, ASCII starts at 32. Hence the translation
      }
    }
}

void displayClear()
{
  unsigned char i=0, j=0;
  sendCommand(CMD_DISPLAY_OFF);     //display off
  for(j=0;j<8;j++)
  {        
    setTextXY(j,0);                                                                            
    {   
      for(i=0;i<16;i++)  //clear all columns
      {   
        displayString((const char*)" ");         
      }   
    }   
  }
  sendCommand(CMD_DISPLAY_ON);     //display on
  setTextXY(0,0);   
}
