<Query Kind="Program">
  <Reference Relative="HtmlAgilityPack 1.4.6\Net45\HtmlAgilityPack.dll">C:\Users\Mike\GitHub\skyhigh\utility\HtmlAgilityPack 1.4.6\Net45\HtmlAgilityPack.dll</Reference>
  <Reference>&lt;ProgramFilesX86&gt;\Microsoft XNA\XNA Game Studio\v4.0\References\Windows\x86\Microsoft.Xna.Framework.dll</Reference>
  <Reference Relative="..\..\..\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll">C:\Users\Mike\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Namespace>HtmlAgilityPack</Namespace>
  <Namespace>Microsoft.Xna.Framework</Namespace>
  <Namespace>System.IO.Compression</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>Newtonsoft.Json</Namespace>
</Query>

void Main()
{
	var startTime = DateTime.UtcNow;
	
	foreach (var filename in Directory.EnumerateFiles(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\test"))
	{
		Stopwatch watch = new Stopwatch();
		watch.Start();
		
		string html;
		
		using (var fs = new FileStream(filename, FileMode.Open))
		using (var gzip = new GZipStream(fs, CompressionMode.Decompress))
		using (StreamReader reader = new StreamReader(gzip, Encoding.UTF8))
		{
			html = reader.ReadToEnd();
		}
		
		//try
		//{
			var doc = new HtmlDocument();
			doc.LoadHtml(html);
			
			if (doc.DocumentNode.Descendants("div").Any(x => HasClass(x, "Unavailable")))
				continue;
				
			var product = new Product();
			product.Url = FilenameToUrl(filename).Dump();
			
			var breadcrumbs = doc.GetElementbyId("breadcrumb");
			var catChain = breadcrumbs.Descendants().Where(x => GetAttribute(x, "data-analytics-type") == "cat").Select(x => GetAttribute(x, "data-analytics-value"));
			catChain.Aggregate(rootCategory, (last, cur) => UpdateCategories(cur, last));
			product.Category = catChain.Last();
			
			product.Title = breadcrumbs.Descendants("li").Last().InnerText.Trim();
			product.ShortDescription = doc.GetElementbyId("product-desc").Descendants().Where(x => GetAttribute(x, "class") == "description").Select(x => x.InnerText.Trim()).FirstOrDefault();
			product.Description = doc.DocumentNode.Descendants().First(x => HasClass(x, "productDescription"))
				.Descendants().Where(x => HasClass(x, "description")).Select(x => x.InnerText.Trim()).FirstOrDefault();
			product.Image = doc.GetElementbyId("slider").Descendants("img").First().GetAttributeValue("src", null);
				
			product.Dump();
		/*}
		catch (Exception ex)
		{
			Console.WriteLine("Exception in {0}: {1}", filename, ex);
		}*/
			
		watch.Stop();
		Console.WriteLine("Parsed {0} in {1} ms", filename, watch.Elapsed.TotalMilliseconds);
	}
	
	JsonConvert.SerializeObject(rootCategory, Newtonsoft.Json.Formatting.Indented).Dump();
	//File.WriteAllText(storePath, JsonConvert.SerializeObject(stores.Values, Newtonsoft.Json.Formatting.Indented));
	
	Console.WriteLine("Complete in {0}", DateTime.UtcNow - startTime);
}

// Define other methods and classes here
Dictionary<string, Category> categories = new Dictionary<string, Category>();
Category rootCategory = new Category();
	
Category UpdateCategories(string name, Category parent)
{
	if (!categories.ContainsKey(name))
	{
		var cat = new Category();
		parent[name] = cat;
		categories[name] = cat;
	}
	
	return categories[name];
}

string FilenameToUrl(string filename)
{
	return "http://www.walmart.ca/en/ip/" + Path.GetFileNameWithoutExtension(filename).Replace('#', '/');
}

string GetAttribute(HtmlNode node, string name)
{
	return node.Attributes[name] == null ? null : node.Attributes[name].Value;
}

bool HasClass(HtmlNode node, string className)
{
	return node.Attributes["class"] == null ? false : node.Attributes["class"].Value.Split(' ').Any(x => x == className);
}

public class Product
{
	public string Title;
	public string ShortDescription;
	public string Description;
	public string Image;
	public string Url;
	public string Category;
	public decimal OnlinePrice;
	public double Rating;
	public int NumRatings;
	public string WalmartItemNumber;
	public string Upc;
	public string Model;
	public string MCN;
	public string Group;
	public string Sku;
	public StoreAvailability Stores;
}

public class StoreAvailability
{
	public int StoreID;
	public decimal Price;
	public Availability Availability;
}

public enum Availability { InStock, Limited, OutOfStock, NotAvailable, NoInformation }

class Category : Dictionary<string, IDictionary>
{
	public new Category this[string key]
	{
		get { return (Category)base[key]; }
		set { base[key] = value; }
	}
}