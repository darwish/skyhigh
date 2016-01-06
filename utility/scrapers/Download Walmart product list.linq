<Query Kind="Program">
  <Reference>&lt;ProgramFilesX86&gt;\Microsoft XNA\XNA Game Studio\v4.0\References\Windows\x86\Microsoft.Xna.Framework.dll</Reference>
  <Reference Relative="..\..\..\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll">C:\Users\Mike\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Namespace>Microsoft.Xna.Framework</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>Newtonsoft.Json</Namespace>
</Query>

void Main()
{
	string mapPath = @"C:\Users\Mike\GitHub\skyhigh\data\walmart\sitemap-index-en.xml";
	XNamespace ns = "http://www.sitemaps.org/schemas/sitemap/0.9";
	var doc = XDocument.Load(mapPath);

	var productMaps = doc.Descendants(ns + "loc")
		.Select(x => x.Value)
		.Where(x => x.StartsWith("http://www.walmart.ca/sitemap-product"))
		.OrderBy(x => int.Parse(new string(x.AsEnumerable().Where(c => char.IsDigit(c)).ToArray())));
		
	var maps = new List<string>();
	foreach (var url in productMaps)
	{
		Console.WriteLine("Downloading {0}", url);
		WebClient client = new WebClient();
		client.Headers[HttpRequestHeader.Accept] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
		var productDoc = XDocument.Parse(client.DownloadString(url));
		maps.AddRange(productDoc.Descendants(ns + "loc").Select(x => x.Value));		
	}
	
	File.WriteAllLines(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\products.txt", maps);
	Console.WriteLine("{0} lines", maps.Count);
}

// Define other methods and classes here
int StringCount(string s, char c)
{	
	int count = 0;
	for (int i = 0; i < s.Length; i++)
		if (s[i] == c)
			count++;
			
	return count;
}