using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;
using System.Xml;
using System.Text.RegularExpressions;

namespace CCB_Event_Parser
{
    public class CampusResource
    {
        public string Name;
        public int Id;

        public CampusResource(string name, int id)
        {
            Name = name;
            Id = id;
        }
    }

    public class Event
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Description;
        public string LeaderNotes;
        public string RecurranceDescription;
        public string Group;
        public string Organizer;
        public int OrganizerId;
        public string Creator;
        public int CreatorId;
        public string ModifiedBy;
        public int ModifiedById;
        public int GroupId;
        public int GroupingId;
        public string EventGroup;

        public DateTime StartDateTime;
        public DateTime EndDateTime;
        public DateTime CreatedDateTime;
        public DateTime ModifiedDateTime;
        public DateTime SetupStartDateTime;
        public DateTime SetupEndDateTime;
        public bool hasSetup;

        public List<CampusResource> EventResources;
        public List<DateTime> Exceptions;

        public Event Clone()
        {
            Event newEvent = new Event(Id);

            newEvent.Id = Id;
            newEvent.Name = Name;
            newEvent.Description = Description;
            newEvent.LeaderNotes = LeaderNotes;

            newEvent.RecurranceDescription = RecurranceDescription;
            newEvent.Group = Group;
            newEvent.Organizer = Organizer;
            newEvent.OrganizerId = OrganizerId;
            newEvent.Creator = Creator;
            newEvent.CreatorId = CreatorId;
            newEvent.ModifiedBy = ModifiedBy;
            newEvent.ModifiedById = ModifiedById;
            newEvent.GroupId = GroupId;
            newEvent.GroupingId = GroupingId;
            newEvent.EventGroup = EventGroup;
            newEvent.StartDateTime = StartDateTime;
            newEvent.EndDateTime = EndDateTime;
            newEvent.CreatedDateTime = CreatedDateTime;
            newEvent.ModifiedDateTime = ModifiedDateTime;
            newEvent.SetupStartDateTime = SetupStartDateTime;
            newEvent.SetupEndDateTime = SetupEndDateTime;
            newEvent.hasSetup = hasSetup;

            newEvent.EventResources = EventResources;
            newEvent.Exceptions = Exceptions;

            return newEvent;
        }

        public Event(int id)
        {
            Id = id;
            Name = "";
            Description = "";
            LeaderNotes = "";
            Group = "";
            Organizer = "";
            GroupId = -1;
            GroupingId = -1;
            CreatorId = -1;
            ModifiedBy = "";
            ModifiedById = -1;
            OrganizerId = -1;
            hasSetup = false;
            RecurranceDescription = "";
            EventResources = new List<CampusResource>();
            Exceptions = new List<DateTime>();
        }
    }

    public class CCBEventParser
    {
        public List<Event> eventList;
        public Dictionary<int, string> CampusResources;
        public Dictionary<int, string> EventGroups;

        public CCBEventParser()
        {
            eventList = new List<Event>();
            CampusResources = new Dictionary<int,string>();
            EventGroups = new Dictionary<int, string>();
        }

        #region XML_Parsing

        private void _processResources(XmlReader resourceXML, Event currentEvent)
        {
            while (resourceXML.ReadToFollowing("resource"))
            {
                string resourceName = "";
                int resourceId = 0;
                string temp = resourceXML.GetAttribute("id");
                if (temp != "")
                    resourceId = Convert.ToInt32(temp);
                resourceXML.ReadToFollowing("name");
                resourceXML.Read();
                if (resourceXML.NodeType == XmlNodeType.Text)
                    resourceName = resourceXML.Value;
                currentEvent.EventResources.Add(new CampusResource(resourceName, resourceId));
                if(!CampusResources.ContainsKey(resourceId))
                    CampusResources.Add(resourceId, resourceName);
            }
        }

        private void _processExceptions(XmlReader resourceXML, Event currentEvent)
        {
            while (resourceXML.ReadToFollowing("exception"))
            {
                string exceptionDate = "";
                int resourceId = 0;
                string temp = resourceXML.GetAttribute("id");
                if (temp != "")
                    resourceId = Convert.ToInt32(temp);
                resourceXML.ReadToFollowing("date");
                resourceXML.Read();
                if (resourceXML.NodeType == XmlNodeType.Text)
                    exceptionDate = resourceXML.Value;

                Regex untilR = new Regex("(?<year>\\d+)-(?<month>\\d+)-(?<day>\\d+)");
                Match untilM = untilR.Match(exceptionDate);

                int year = Convert.ToInt32(untilM.Result("${year}"));
                int month = Convert.ToInt32(untilM.Result("${month}"));
                int day = Convert.ToInt32(untilM.Result("${day}"));
                currentEvent.Exceptions.Add(new DateTime(year,month, day));
            }
        }

        private void _parseEvent(XmlReader singleEvent, int eventId)
        {
            Event currentEvent = new Event(eventId);
            eventList.Add(currentEvent);
            string temp;

            while (singleEvent.Read())
            {
                switch (singleEvent.NodeType)
                {
                    case XmlNodeType.Element:
                        switch (singleEvent.Name)
                        {
                            case "created":
                                singleEvent.Read();
                                if(singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.CreatedDateTime = DateTime.Parse(singleEvent.Value);
                                break;

                            case "modified":
                                singleEvent.Read();
                                if(singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.ModifiedDateTime = DateTime.Parse(singleEvent.Value);
                                break;

                            case "start_datetime":
                                singleEvent.Read();
                                if(singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.StartDateTime = DateTime.Parse(singleEvent.Value);
                                break;

                            case "end_datetime":
                                singleEvent.Read();
                                if (singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.EndDateTime = DateTime.Parse(singleEvent.Value);
                                break;

                            case "name":
                                singleEvent.Read();
                                if(singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.Name = singleEvent.Value;
                                break;

                            case "description":
                                singleEvent.Read();
                                if (singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.Description = singleEvent.Value;
                                break;

                            case "leader_notes":
                                singleEvent.Read();
                                if (singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.LeaderNotes = singleEvent.Value;
                                break;

                            case "recurrence_description":
                                singleEvent.Read();
                                if (singleEvent.NodeType == XmlNodeType.Text)
                                    currentEvent.RecurranceDescription = singleEvent.Value;
                                break;

                            case "group":
                                temp = singleEvent.GetAttribute("id");
                                if(temp != "")
                                    currentEvent.GroupId = Convert.ToInt32(temp);
                                while (singleEvent.NodeType != XmlNodeType.EndElement)
                                {
                                    singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Text)
                                        currentEvent.Group = singleEvent.Value;
                                }
                                break;

                            case "event_grouping":
                                temp = singleEvent.GetAttribute("id");
                                if(temp != "")
                                    currentEvent.GroupingId = Convert.ToInt32(temp);
                                while (singleEvent.NodeType != XmlNodeType.EndElement)
                                {
                                    singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Text)
                                        currentEvent.EventGroup = singleEvent.Value;
                                }
                                break;

                            case "organizer":
                                temp = singleEvent.GetAttribute("id");
                                if(temp != "")
                                    currentEvent.OrganizerId = Convert.ToInt32(temp);
                                while (singleEvent.NodeType != XmlNodeType.EndElement)
                                {
                                    singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Text)
                                        currentEvent.Organizer = singleEvent.Value;
                                }
                                break;

                            case "creator":
                                temp = singleEvent.GetAttribute("id");
                                if(temp != "")
                                    currentEvent.CreatorId = Convert.ToInt32(temp);
                                while (singleEvent.NodeType != XmlNodeType.EndElement)
                                {
                                    singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Text)
                                        currentEvent.Creator = singleEvent.Value;
                                }
                                break;

                            case "modifier":
                                temp = singleEvent.GetAttribute("id");
                                if(temp != "")
                                    currentEvent.ModifiedById = Convert.ToInt32(temp);
                                while (singleEvent.NodeType != XmlNodeType.EndElement)
                                {
                                    singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Text)
                                        currentEvent.ModifiedBy = singleEvent.Value;
                                }
                                break;

                            case "resources":
                                _processResources(singleEvent.ReadSubtree(), currentEvent);
                                break;

                            case "exceptions":
                                _processExceptions(singleEvent.ReadSubtree(), currentEvent);
                                break;

                            case "event":
                                break;

                            //skip unknown subtrees
                            default:
                                if (singleEvent.IsEmptyElement)
                                    break;
                                int closeBalance = 1;
                                bool stop = false;
                                while (closeBalance != 0 && !stop)
                                {
                                    stop = !singleEvent.Read();
                                    if (singleEvent.NodeType == XmlNodeType.Element && !singleEvent.IsEmptyElement)
                                        closeBalance++;
                                    if (singleEvent.NodeType == XmlNodeType.EndElement)
                                        closeBalance--;
                                }
                                break;
                        }
                        break;
                }
            }
        }

        #endregion

        #region EventFuncs
        public bool TimeFrameIncludesEvent(DateTime startTime, DateTime endTime, Event e){
            if (e.StartDateTime >= endTime)
                return false;

            foreach (Event ev in AllEventInstancesInRange(startTime, endTime, e))
                if (ev.StartDateTime < endTime && ev.EndDateTime > startTime)
                    return true;

            return false;
        }

        public List<Event> AllEventsInTimeRange(DateTime startTime, DateTime endTime)
        {
            List<Event> ret = new List<Event>();
            foreach (Event e in eventList)
            {
                if(e.StartDateTime < endTime && e.EndDateTime > startTime)
                    ret.Add(e);
                ret.AddRange(AllEventInstancesInRange(startTime, endTime, e));
            }

            foreach (Event e in ret.ToList())
                foreach (DateTime t in e.Exceptions)
                    if (e.StartDateTime.Date == t.Date)
                        ret.Remove(e);

            return ret;
        }

        public List<Event> AllEventInstancesInRange(DateTime startTime, DateTime endTime, Event e)
        {
            List<Event> RelevantTimes = new List<Event>();
            RelevantTimes.AddRange(CalculateDailyRecurrancesInRange(startTime, endTime, e));
            RelevantTimes.AddRange(CalculateWeeklyRecurrancesInRange(startTime, endTime, e));
            RelevantTimes.AddRange(CalculateMonthlyRecurrancesInRange(startTime, endTime, e));
            return RelevantTimes;
        }

        public List<Event> CalculateDailyRecurrancesInRange(DateTime startTime, DateTime endTime, Event e)
        {
            List<Event> ret = new List<Event>();
            DateTime untilDateTime = new DateTime();
            bool hasUntil = false;

            if (e.RecurranceDescription.Substring(0, 8) != "Every da")
                return ret;

            if (endTime < e.StartDateTime)
                return ret;

            Regex untilR = new Regex("until (?<month>\\w+) (?<day>\\d+), (?<year>\\d{4})");
            Match untilM = untilR.Match(e.RecurranceDescription);

            if (untilM.Success)
            {
                hasUntil = true;
                int year = Convert.ToInt32(untilM.Result("${year}"));
                int day = Convert.ToInt32(untilM.Result("${day}"));
                int month = 0;
                switch (untilM.Result("${month}"))
                {
                    case "Jan": month = 1; break;
                    case "Feb": month = 2; break;
                    case "Mar": month = 3; break;
                    case "Apr": month = 4; break;
                    case "May": month = 5; break;
                    case "Jun": month = 6; break;
                    case "Jul": month = 7; break;
                    case "Aug": month = 8; break;
                    case "Sep": month = 9; break;
                    case "Oct": month = 10; break;
                    case "Nov": month = 11; break;
                    case "Dec": month = 12; break;
                }
                untilDateTime = new DateTime(year, month, day, 23, 59, 59);
            }

            if (hasUntil && untilDateTime < startTime)
                return ret;

            DateTime temp = startTime;

            if(temp < e.StartDateTime)
                temp = e.StartDateTime;

            while (temp <= endTime)
            {
                Event newEvent = e.Clone();
                newEvent.StartDateTime = new DateTime(temp.Year, temp.Month, temp.Day, e.StartDateTime.Hour, e.StartDateTime.Minute, e.StartDateTime.Second);
                newEvent.EndDateTime = newEvent.StartDateTime + (e.EndDateTime - e.StartDateTime);
                if (hasUntil && newEvent.StartDateTime > untilDateTime)
                    return ret;
                if(newEvent.StartDateTime.Date != e.StartDateTime.Date)
                    ret.Add(newEvent);
                temp = temp.AddDays(1);
            }

            return ret;
        }

        public List<Event> CalculateWeeklyRecurrancesInRange(DateTime startTime, DateTime endTime, Event e)
        {
            List<Event> ret = new List<Event>();
            DateTime untilDateTime = new DateTime();
            bool hasUntil = false;

            if (e.RecurranceDescription.Substring(0, 8) != "Every we")
                return ret;

            if (endTime < e.StartDateTime)
                return ret;

            Regex untilR = new Regex("until (?<month>\\w+) (?<day>\\d+), (?<year>\\d{4})");
            Match untilM = untilR.Match(e.RecurranceDescription);

            if (untilM.Success)
            {
                hasUntil = true;
                int year = Convert.ToInt32(untilM.Result("${year}"));
                int day = Convert.ToInt32(untilM.Result("${day}"));
                int month = 0;
                switch (untilM.Result("${month}"))
                {
                    case "Jan": month = 1; break;
                    case "Feb": month = 2; break;
                    case "Mar": month = 3; break;
                    case "Apr": month = 4; break;
                    case "May": month = 5; break;
                    case "Jun": month = 6; break;
                    case "Jul": month = 7; break;
                    case "Aug": month = 8; break;
                    case "Sep": month = 9; break;
                    case "Oct": month = 10; break;
                    case "Nov": month = 11; break;
                    case "Dec": month = 12; break;
                }
                untilDateTime = new DateTime(year, month, day, 23, 59, 59);
            }

            if (hasUntil && untilDateTime < startTime)
                return ret;

            DateTime searchDate1 = startTime;
            DateTime searchDate2;

            for (int i = 0; i < 7; i++)
            {
                if (searchDate1 > endTime)
                    break;
                if (e.RecurranceDescription.Contains(searchDate1.DayOfWeek.ToString()))
                {
                    searchDate2 = searchDate1;
                    while(searchDate2 <= endTime){
                        if (hasUntil)
                            if (searchDate2 > untilDateTime)
                                break;
                        Event newEvent = e.Clone();
                        newEvent.StartDateTime = new DateTime(searchDate2.Year, searchDate2.Month, searchDate2.Day, e.StartDateTime.Hour, e.StartDateTime.Minute, e.StartDateTime.Second);
                        newEvent.EndDateTime = newEvent.StartDateTime + (e.EndDateTime - e.StartDateTime);
                        if(newEvent.StartDateTime.Date != e.StartDateTime.Date && newEvent.StartDateTime >= e.StartDateTime)
                            ret.Add(newEvent);
                        searchDate2 = searchDate2.AddDays(7);
                    }
                }
                searchDate1 = searchDate1.AddDays(1);
            }

            return ret;
        }

        public List<Event> CalculateMonthlyRecurrancesInRange(DateTime startTime, DateTime endTime, Event e)
        {
            List<Event> ret = new List<Event>();
            DateTime untilDateTime = new DateTime();
            bool hasUntil = false;

            if (e.RecurranceDescription.Substring(0, 8) != "Every mo")
                return ret;

            if (e.Id == 674)
                hasUntil = false;

            if (endTime < e.StartDateTime)
                return ret;

            Regex untilR = new Regex("until (?<month>\\w+) (?<day>\\d+), (?<year>\\d{4})");
            Match untilM = untilR.Match(e.RecurranceDescription);

            if (untilM.Success){
                hasUntil = true;
                int year = Convert.ToInt32(untilM.Result("${year}"));
                int day = Convert.ToInt32(untilM.Result("${day}"));
                int month = 0;
                switch (untilM.Result("${month}")){
                    case "Jan": month = 1; break;
                    case "Feb": month = 2; break;
                    case "Mar": month = 3; break;
                    case "Apr": month = 4; break;
                    case "May": month = 5; break;
                    case "Jun": month = 6; break;
                    case "Jul": month = 7; break;
                    case "Aug": month = 8; break;
                    case "Sep": month = 9; break;
                    case "Oct": month = 10; break;
                    case "Nov": month = 11; break;
                    case "Dec": month = 12; break;
                }
                untilDateTime = new DateTime(year, month, day, 23, 59, 59);
            }

            if (hasUntil && untilDateTime < startTime)
                return ret;

            Regex monthlyR = new Regex("the (?<occurance>\\w+) (?<day>\\w+) of the month");

            DateTime startMonth = new DateTime(startTime.Year, startTime.Month, 1);

            while (startMonth.Year <= endTime.Year && startMonth.Month <= endTime.Month)
            {
                foreach (Match m in monthlyR.Matches(e.RecurranceDescription))
                {
                    DayOfWeek dayOfWeek = DayOfWeek.Sunday;
                    switch(m.Groups["day"].Value)
                    {
                        case "Monday": dayOfWeek = DayOfWeek.Monday; break;
                        case "Tuesday": dayOfWeek = DayOfWeek.Tuesday; break;
                        case "Wednesday": dayOfWeek = DayOfWeek.Wednesday; break;
                        case "Thursday": dayOfWeek = DayOfWeek.Thursday; break;
                        case "Friday": dayOfWeek = DayOfWeek.Friday; break;
                        case "Saturday": dayOfWeek = DayOfWeek.Saturday; break;
                        case "Sunday": dayOfWeek = DayOfWeek.Sunday; break;
                    }

                    int occurance = 0;
                    switch (m.Groups["occurance"].Value)
                    {
                        case "first": occurance = 1; break;
                        case "second": occurance = 2; break;
                        case "third": occurance = 3; break;
                        case "fourth": occurance = 4; break;
                        case "fifth": occurance = 5; break;
                    }

                    for(int i = 1;i<= DateTime.DaysInMonth(startMonth.Year,startMonth.Month);i++)
                    {
                        if ((new DateTime(startMonth.Year, startMonth.Month, i)).DayOfWeek == dayOfWeek)
                            occurance--;
                        if (occurance == 0){
                            occurance = i;
                            break;
                        }
                    }
                    DateTime start = new DateTime(startMonth.Year, startMonth.Month, occurance, e.StartDateTime.Hour, e.StartDateTime.Minute, e.StartDateTime.Second);
                    DateTime end = start + (e.EndDateTime - e.StartDateTime);
                    if (!(hasUntil && start > untilDateTime) && start < endTime && end > startTime && start.Date != e.StartDateTime.Date)
                    {
                        Event newE = e.Clone();
                        newE.StartDateTime = start;
                        newE.EndDateTime = end;
                        ret.Add(newE);
                    }
                }
                startMonth = startMonth.AddMonths(1);
                if (hasUntil && startMonth > untilDateTime)
                    break;
            }
            return ret;
        }

        #endregion

        public List<Event> narrowByResource(List<Event> eventList,int resourceId)
        {
            List<Event> ret = new List<Event>();
            ret = eventList.Where(item => item.EventResources.Any(c => c.Id == resourceId)).ToList();
            return ret;
        }

        public List<Event> sortListByTime(List<Event> eventList)
        {
            return eventList.OrderBy(x => x.StartDateTime).ToList();
        }

        public void readFile(string file){
            XmlReader mainReader = XmlReader.Create(file);
            XmlReader eventReader;

            while (mainReader.ReadToFollowing("event"))
            {
                eventReader = mainReader.ReadSubtree();
                _parseEvent(eventReader, Convert.ToInt32(mainReader.GetAttribute("id")));
            }
        }

        public void readFile(Stream input)
        {
            XmlReader mainReader = XmlReader.Create(input);
            XmlReader eventReader;

            while (mainReader.ReadToFollowing("event"))
            {
                eventReader = mainReader.ReadSubtree();
                _parseEvent(eventReader, Convert.ToInt32(mainReader.GetAttribute("id")));
            }
        }
    }
}
