<Query Kind="Program">
  <Reference Relative="..\..\..\..\Downloads\HtmlAgilityPack.1.4.6\Net45\HtmlAgilityPack.dll">E:\Users\Michael\Downloads\HtmlAgilityPack.1.4.6\Net45\HtmlAgilityPack.dll</Reference>
  <Reference Relative="..\..\..\..\Downloads\Json70r1\Bin\Net45\Newtonsoft.Json.dll">E:\Users\Michael\Downloads\Json70r1\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Reference>&lt;RuntimeDirectory&gt;\System.Net.Http.dll</Reference>
  <Reference>&lt;RuntimeDirectory&gt;\System.Web.Extensions.dll</Reference>
  <Namespace>HtmlAgilityPack</Namespace>
  <Namespace>Newtonsoft.Json</Namespace>
  <Namespace>System.IO</Namespace>
  <Namespace>System.IO.Ports</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Http</Namespace>
  <Namespace>System.Net.Http.Headers</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>System.Threading.Tasks</Namespace>
  <Namespace>System.Web.Script.Serialization</Namespace>
</Query>

void Main()
{
	string query = "toaster";
	string url = string.Format("http://www.redflagdeals.com/search/?q={0}&section=offers&page=1&timeframe=any", query);
	string storesPath = @"E:\Users\Michael\Documents\GitHub\skyhigh\data\stores.json";
	var client = new WebClient();
	
	client.Headers[HttpRequestHeader.Accept] = "application/json";
	client.Headers["X-Requested-With"] = "XMLHttpRequest";
	
	HtmlDocument doc = new HtmlDocument();
	string pageString = client.DownloadString(url);
	var html = JsonConvert.DeserializeAnonymousType(pageString, new { html = "" }).html;
	doc.LoadHtml(html);
	var nodes = doc.DocumentNode.SelectNodes("/*[@class='offer_normal']");
	//nodes.First().InnerHtml.Dump();
	
	var stores = new Dictionary<int, Shop>();
	try 
	{
		foreach (var store in JsonConvert.DeserializeObject<Shop[]>(File.ReadAllText(storesPath)))
			stores[store.reference] = store;
	}
	catch (Exception) { }

	
	var items = nodes.Select((x, i) => 
	{
		Console.WriteLine("Fetching item {0} of {1}", i, nodes.Count);
		
		string title = x.Descendants("a").Where(y => y.ParentNode.Name == "h3").First().InnerText;
		string itemPage = "http://www.redflagdeals.com" + x.Element("a").Attributes["href"].Value;		
		
		var doc2 = new HtmlDocument();
		doc2.LoadHtml(client.DownloadString(itemPage));
		
		int storeID = int.Parse(doc2.DocumentNode.SelectSingleNode("//*[@id='main_block']").Attributes["data-mlr-merchant-id"].Value);

		stores[storeID] = new Shop() 
		{
			name = doc2.DocumentNode.SelectSingleNode("//*[@id='merchant_name']").InnerText,
			reference = storeID
		};
			
		return new Item()
		{
			name = title.Substring(0, title.IndexOf(" - $")),
			price = decimal.Parse(Regex.Match(title, @"\$(\d+\.\d\d)").Groups[1].Value),
			image = doc2.DocumentNode.SelectSingleNode("//*[@id='side_block']/a/img").Attributes["src"].Value,
			description = doc2.DocumentNode.SelectSingleNode("//*[@id='description']").InnerText.Trim(),
			url =  doc2.DocumentNode.SelectSingleNode("//*[@id='side_block']/a").Attributes["href"].Value,
			category = x.Descendants("a").Where(y => y.ParentNode.ParentNode.Name == "ul" && y.ParentNode.ParentNode.Attributes["class"].Value == "related").First().InnerText,
			shopReference = storeID
		};
	}).ToList();
	
	File.WriteAllText(storesPath, JsonConvert.SerializeObject(stores.Values, Newtonsoft.Json.Formatting.Indented));
	
	string json = JsonConvert.SerializeObject(items, Newtonsoft.Json.Formatting.Indented);
	File.WriteAllText(@"E:\Users\Michael\Documents\GitHub\skyhigh\data\" + query + ".json", json);
	Console.WriteLine("\n");
	Console.WriteLine(json);
}

// Define other methods and classes here
class Item
{
	public int shopReference;
	public string image;
	public string name;
	public string description;
	public string url;
	public string category;
	public decimal price;
	public double discount;
}

class Shop
{
	public int reference;
	public string name;
	public string address;
	public double longitude;
	public double latitude;
}