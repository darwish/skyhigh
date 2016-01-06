<Query Kind="Program">
  <Reference>&lt;ProgramFilesX86&gt;\Microsoft XNA\XNA Game Studio\v4.0\References\Windows\x86\Microsoft.Xna.Framework.dll</Reference>
  <Reference Relative="..\..\..\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll">C:\Users\Mike\Downloads\Json60r8\Bin\Net45\Newtonsoft.Json.dll</Reference>
  <Reference>&lt;RuntimeDirectory&gt;\System.IO.Compression.dll</Reference>
  <Namespace>Microsoft.Xna.Framework</Namespace>
  <Namespace>System.Net</Namespace>
  <Namespace>System.Net.Sockets</Namespace>
  <Namespace>System.Text.RegularExpressions</Namespace>
  <Namespace>Newtonsoft.Json</Namespace>
  <Namespace>System.Dynamic</Namespace>
  <Namespace>System.IO.Compression</Namespace>
  <Namespace>System.Threading.Tasks</Namespace>
  <Namespace>System.Collections.Concurrent</Namespace>
</Query>

void Main()
{
	var startTime = DateTime.UtcNow;
	
	string[] productUrls = File.ReadAllLines(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\products.txt");
	var savePageIDs = new HashSet<string>(Directory.GetFiles(productPagesPath).Select(x => Path.GetFileNameWithoutExtension(x)).Select(x => x.Substring(x.LastIndexOf('#') + 1)));
	productUrls = productUrls.Where(x => !savePageIDs.Contains(x.Substring(x.LastIndexOf('/') + 1))).ToArray();

	int failed = 0;
	
	for (int i = 0; i < productUrls.Length; i += 100)
	{
		var batchStart = DateTime.UtcNow;
		
		Parallel.ForEach(productUrls.Skip(i).Take(100), url => 
		{
			Stopwatch watch = new Stopwatch();
			watch.Start();
			
			try
			{
				var client = new WebClient();
				client.Encoding = Encoding.UTF8;
				client.Headers[HttpRequestHeader.Accept] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
				client.Headers[HttpRequestHeader.AcceptEncoding] = "gzip";
				byte[] html = client.DownloadData(url);
				string filename = url.Substring("http://www.walmart.ca/en/ip/".Length).Replace('/', '#');
				Save(filename, html);
			}
			catch (Exception ex)
			{
				failed++;
				Console.WriteLine("Exception in {0}: {1}", url, ex);
			}
			
			watch.Stop();
			Console.WriteLine("Saved {0} in {1:f2} s", url, watch.Elapsed.TotalSeconds);
		});
		
		Console.WriteLine("Saved {0} pages in {1}.", Math.Min(productUrls.Length - i, 100), DateTime.UtcNow - batchStart);
		Console.WriteLine("{0:f1}% done.", 100.0 * i / productUrls.Length);
	}
	
	Console.WriteLine("Complete in {0}", DateTime.UtcNow - startTime);
	Console.WriteLine("{0} failed of {1}", failed, productUrls.Length);
}

// Define other methods and classes here
public const string productPagesPath = @"C:\Users\Mike\GitHub\skyhigh\data\walmart\product pages\";
void Save(string filename, byte[] contents)
{
	File.WriteAllBytes(productPagesPath + filename + ".gz", contents);
}

/*void Save(string filename, string contents)
{
	using (var memoryStream = new MemoryStream())
	{
		using (var archive = new ZipArchive(memoryStream, ZipArchiveMode.Create, true))
		{
			var demoFile = archive.CreateEntry(filename + ".html");
		
			using (var entryStream = demoFile.Open())
			using (var streamWriter = new StreamWriter(entryStream))
			{
				streamWriter.Write(contents);
			}
		}
		
		using (var fileStream = new FileStream(@"C:\Users\Mike\GitHub\skyhigh\data\walmart\product pages\" + filename + ".zip", FileMode.Create))
		{
			memoryStream.Seek(0, SeekOrigin.Begin);
			memoryStream.CopyTo(fileStream);
		}
	}
}*/