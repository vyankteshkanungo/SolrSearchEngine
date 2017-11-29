

import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.Arrays;

import org.apache.tika.exception.TikaException;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.Parser;
import org.apache.tika.parser.html.HtmlParser;
import org.apache.tika.sax.BodyContentHandler;
import org.xml.sax.SAXException;

public class Extracthtml {
	static int count = 0;

	public static void main(final String[] args) throws IOException, SAXException, TikaException {

		Parser parser = new HtmlParser();
		System.out.println("Extracting the content from a HTML File:");
		File dir = new File("/adrive/CNNData/CNNDownloadData/");
		File[] files = dir.listFiles();
		PrintWriter prnt = new PrintWriter(new FileWriter("big.txt"));
		Arrays.stream(files).forEach((file) -> {
			try {
				extractContentFromFile(parser, file.getName(), prnt);
			} catch (Exception e) {
				e.printStackTrace();
			}
		});
		prnt.close();
		System.out.println(count);
	}

	private static void extractContentFromFile(final Parser parser, final String fileName, PrintWriter prnt)
			throws IOException, SAXException, TikaException {
		
		BodyContentHandler handler = new BodyContentHandler(-1);
		Metadata metadata = new Metadata();
		FileInputStream content = new FileInputStream(new File("/adrive/CNNData/CNNDownloadData/"+fileName));
		count++;
		parser.parse(content, handler, metadata, new ParseContext());
		prnt.println(handler.toString().replaceAll("[\r\n]+", "\n"));
	}
}