# epdash
ESP8266 + E-paper wireless dashboard

It is a complete project, from 3D design to microcontroller program and webserver helper.

Main components are:
- the e-paper display: Waveshare 7.5 black/red display => https://www.waveshare.com/wiki/7.5inch_e-Paper_HAT_(B)
- trigBoard from Kevin Darrah: https://www.kevindarrah.com/wiki/index.php?title=TrigBoard
- EPaperBoard also from Kevin Darrah: https://www.kevindarrah.com/wiki/index.php?title=EPaperBoard
- 2 pcs. Li-Ion batteries 3500mAh each that should power this device for quite some time

Trigboard is a ESP8266 ultra low power standby device. It wakes up and powers the ESP and display and then goes back to ULP sleep. The timer can be programmed by changing a resistor and I chose one that wakes up the device once every ~30 minutes.

After waking up the ESP connects to a intranet web server and fetches a 640x384 pixel bmp file that then displays. After finishing it signals the timer chip that it is done and power is removed. Countdown of a new sleep period begins.

My goal was a complete freedom to setup the display. Using the ESP to generate the display data meant always changing the programming on the chip, updating its flash and so on. Pretty cumbersome. Moving the display data creation to a local webserver and fetching just the bmp file allowed me change anything at any time, without bothering the display unit itself.

Initial project was a weather display. Data was retreived by json and set up on the ESP, total time to update the display was about 300 seconds. Now I serve just the bmp file, generated on the fly, and the update time was reduced to about 55 seconds with DHCP enabled on the ESP. Probably a few seconds less by manually assigning the IP address.



Sample output:
<img src="https://github.com/cctweaker/epdash/blob/master/webserver/imagini/image.png?raw=true">
