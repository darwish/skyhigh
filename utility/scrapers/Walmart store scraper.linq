<Query Kind="Program">
  <Reference Relative="HtmlAgilityPack 1.4.6\Net45\HtmlAgilityPack.dll">C:\Users\Mike\GitHub\skyhigh\utility\HtmlAgilityPack 1.4.6\Net45\HtmlAgilityPack.dll</Reference>
  <Reference>&lt;ProgramFilesX86&gt;\Microsoft XNA\XNA Game Studio\v4.0\References\Windows\x86\Microsoft.Xna.Framework.dll</Reference>
  <Reference Relative="..\..\..\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll">C:\Users\Mike\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Namespace>HtmlAgilityPack</Namespace>
  <Namespace>Microsoft.Xna.Framework</Namespace>
  <Namespace>Newtonsoft</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>Newtonsoft.Json</Namespace>
  <Namespace>System.Collections.Concurrent</Namespace>
  <Namespace>System.Threading.Tasks</Namespace>
</Query>

void Main()
{
	string path = @"C:\Users\Mike\GitHub\skyhigh\data\walmart\sitemap-stores-en.xml";
	
	var doc = new XmlDocument();
	doc.Load(path);
	
	var excludeList = new[] { "http://www.walmart.ca/en/walmart-home-office-employees-only-/8122" };
	
	var storeUrls = doc.DocumentElement.GetElementsByTagName("loc").Cast<XmlNode>().Select(x => x.InnerText).Except(excludeList);
	string storePath = @"C:\Users\Mike\GitHub\skyhigh\data\walmart\stores.json";
		
	var stores = JsonConvert.DeserializeObject<Store[]>(File.ReadAllText(storePath)).ToDictionary(x => x.ReferenceID);
	
	Parallel.ForEach(storeUrls, url => 
	{
		Stopwatch watch = new Stopwatch();
		watch.Start();
		
		try
		{
			var client = new WebClient();
			client.Encoding = Encoding.UTF8;
			client.Headers[HttpRequestHeader.Accept] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
			string html = client.DownloadString(url);
			
			var parser = new Parser(html);
			var store = new Store();
			store.Chain = "Walmart";
			store.Url = url;
			
			parser.FindNext("id=hdr-address");
			store.StreetAddress = parser.GetTextAfter("streetAddress");
			store.City = parser.GetTextAfter("addressLocality");
			store.Province = parser.GetTextAfter("addressRegion");
			store.PostalCode = parser.GetTextAfter("postalCode");
			store.Telephone = parser.GetTextAfter("telephone");
			
			parser.FindNext("hdr-store-hours");
			for (int i = 0; i < 7; i++)
				store.OpeningHours[i] = TimeRange.Parse(parser.GetAttribute("datetime").Substring(2));
		
			parser.FindNext("hdr-holiday-hours>");
			while (!parser.IsNext("</ul>"))
				store.HolidayHours.Add(HolidayHours.Parse(parser.GetTextAfter("<li")));
			
			store.Name = parser.GetStringAfter("store.storeName");
			string[] location = parser.GetStringAfter("store.storeLocation").Split(',');
			store.Lattitude = double.Parse(location[0]);
			store.Longitude = double.Parse(location[1]);
			store.ReferenceID = "" + parser.GetIntAfter("store.storeNumber");
			stores[store.ReferenceID] = store;
		}
		catch (Exception ex)
		{
			Console.WriteLine("Exception in {0}: {1}", url, ex);
		}
		
		watch.Stop();
		Console.WriteLine("Parsed {0} in {1} ms", url, watch.Elapsed.TotalMilliseconds);
	});
	
	File.WriteAllText(storePath, JsonConvert.SerializeObject(stores.Values, Newtonsoft.Json.Formatting.Indented));
}

// Define other methods and classes here
class Store
{
	public string Name;
	public string Chain;
	public string ReferenceID;
	public string Url;
	public string StreetAddress;
	public string City;
	public string Province;
	public string PostalCode;
	public string Telephone;
	public double Lattitude;
	public double Longitude;
	
	public TimeRange[] OpeningHours = new TimeRange[7];
	public List<HolidayHours> HolidayHours = new List<HolidayHours>();
}

struct HolidayHours
{
	public DateTime Date;
	public TimeRange Hours;
	
	public static HolidayHours Parse(string s)
	{
		string date = s.Substring(0, s.IndexOf(':'));
		string hours = s.Substring(date.Length + 1);
		
		return new HolidayHours
		{
			Date = DateTime.Parse(date.Substring(0, date.IndexOf('('))),
			Hours = TimeRange.Parse(hours)
		};
	}	
}

struct TimeRange
{
	public DateTime Start, End;
	
	public static TimeRange Parse(string s)
	{
		s = s.Trim();
		if (s.StartsWith("closed", StringComparison.OrdinalIgnoreCase))
			return new TimeRange();
		
		if (s.ToLower() == "24hrs")
			return new TimeRange() { End = DateTime.Parse("11:59:59 pm") };
			
		string[] parts = s.Split('-', '–');
		
		var errorTime = new DateTime(9999, 1, 1);
		var result = new TimeRange();
		if (!DateTime.TryParse(parts[0], out result.Start))
			result.Start = errorTime;
		if (!DateTime.TryParse(parts[1], out result.End))
			result.End = errorTime;
		
		return result;
	}
}

public class Parser
{
	int currentIndex = 0;
	string text;
	
	public Parser(string text)
	{
		this.text = text;
	}
	
	public bool IsNext(string search)
	{
		int index = currentIndex;
		while (char.IsWhiteSpace(text[index]))
			index++;
			
		for (int i = 0; i < search.Length; i++)
			if (search[i] != text[index + i])
				return false;
				
		return true;
	}
	
	public int FindNext(string search)
	{
		int index = text.IndexOf(search, currentIndex);
		if (index < 0)
			return index;
			
		return currentIndex = index;
	}
	
	public int FindTagEnd()
	{
		return currentIndex = text.IndexOf('>', currentIndex) + 1;
	}
	
	public string GetText()
	{
		int start = FindTagEnd();
		currentIndex = text.IndexOf("</", currentIndex);
		string result = text.Substring(start, currentIndex - start);
		currentIndex = text.IndexOf('>', currentIndex) + 1;
		
		return result;
	}
	
	public string GetTextAfter(string search)
	{
		FindNext(search);
		return GetText();
	}
	
	public string GetAttribute(string name)
	{
		FindNext(name);
		currentIndex = text.IndexOf('=', currentIndex) + 1;
		if (text[currentIndex] == '"')
		{
			int start = ++currentIndex;
			currentIndex = text.IndexOf('"', currentIndex) + 1;
			return text.Substring(start, currentIndex - start - 1);
		}
		else
		{
			int start = currentIndex;
			while (text[currentIndex] != ' ' && text[currentIndex] != '>' && text[currentIndex] != '/')
				currentIndex++;

			return text.Substring(start, currentIndex - start);
		}		
	}
	
	public string GetNextString()
	{
		while (text[currentIndex] != '"')
			currentIndex++;
			
		int start = ++currentIndex;
		while (text[currentIndex] != '"' || text[currentIndex - 1] == '\\')
			currentIndex++;
		
		return text.Substring(start, currentIndex++ - start);
	}
	
	public string GetStringAfter(string search)
	{
		FindNext(search);
		return GetNextString();
	}
	
	public int GetNextInt()
	{
		while (!char.IsDigit(text[currentIndex]) && text[currentIndex] != '-')
			currentIndex++;
			
		int start = currentIndex;
		while (char.IsDigit(text[currentIndex]) || (currentIndex == start && text[currentIndex] == '-'))
			currentIndex++;
		
		return int.Parse(text.Substring(start, currentIndex - start));
	}
	
	public int GetIntAfter(string search)
	{
		FindNext(search);
		return GetNextInt();
	}
}