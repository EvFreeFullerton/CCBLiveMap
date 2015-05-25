## CCB Live Map ##
Pulls information from CCB ([Church Community Builder](http://www.churchcommunitybuilder.com)) into a MySQL database and creates a real time interactive map of events occurring on the campus.

Using a SVG map we are able to change the color of which rooms / buildings on campus are being used and display what event is occurring with other metadata regarding it.

## ToDo ##
**Major stuff:**
- Monthly & Daily recurring events (only weekly work right now)
- Dedup json string
- allow for different times
- cleanup map
- Date exceptions
- update DB regularly
- make code more pretty
- move login info to config file

**UI stuff:**
- ~~Slider to select which part of a 24 hour day to display~~
- In an idle state zoom between buildings to provide more detail as to what is happening in that section of the campus
- Display tooltip info when you mouse over an building that has an event occuring in it
- Display security camera in tooltip of what is going on in that room

**Future stuff:**
- Add code to intranet for others to view
- Segment featured based on security level (Facilities & Tech & IT, Upper Management, Admins, Volunteers 

## Ideas ##
- Pull in data from Planning Center
  - Which tech should be where
  - What resources are being used where
  - Where is the tech right now? (From WiFi data maybe?)
- Pull in HVAC data and display info per room
- Pull in WiFi info from PRTG
  - Number of connected people in a room
  - When zoomed in number of connected people to AP in the room
  - Toggle to change the color of the room based on number of connected users in a room
- RMon data on amount of bandwidth being used in a room
- Pull in 911 call info from Mitel
  - Build list of extensions and sort them by building
  - Query the Mitel DB
  - Display where the call came from
