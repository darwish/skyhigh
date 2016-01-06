<Query Kind="Program">
  <Reference>&lt;ProgramFilesX86&gt;\Microsoft XNA\XNA Game Studio\v4.0\References\Windows\x86\Microsoft.Xna.Framework.dll</Reference>
  <Reference Relative="..\..\..\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll">C:\Users\Mike\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Namespace>Microsoft.Xna.Framework</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>System.IO.Compression</Namespace>
</Query>

void Main()
{
	var startTime = DateTime.UtcNow;
	var unavailableProducts = new List<string>();
	int pagesChecked = 0;
	
	foreach (var filename in Directory.EnumerateFiles(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\product pages"))
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
		
		if (html.Contains("class=\"html-cartridge Unavailable"))
			unavailableProducts.Add(Path.GetFileName(filename));
			
		pagesChecked++;
		if ((pagesChecked % 1000) == 0)
			Console.WriteLine("Checked {0} pages.", pagesChecked);
	}
	
	unavailableProducts.Count.Dump();
	File.WriteAllLines(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\unavailable-products.txt", unavailableProducts);
}

// Define other methods and classes here
