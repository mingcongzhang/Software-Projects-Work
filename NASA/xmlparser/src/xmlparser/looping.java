package xmlparser;

import java.io.File;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import org.w3c.dom.Document;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

public class looping {
	public static void main(String[] args){
		try{
			File file = new File("test.xml");
			DocumentBuilder dBuilder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
			Document doc = dBuilder.parse(file);
			System.out.println("Root element : "+ doc.getDocumentElement().getNodeName());
			if(doc.hasChildNodes()){
				printNode(doc.getChildNodes());
				
			}
		}
		catch (Exception e){
			System.out.println(e.getMessage());
		}
	}


	private static void printNode(NodeList nodeList){
		for(int count = 0; count < nodeList.getLength();count++){
			Node tempNode = nodeList.item(count);
			if(tempNode.getNodeType() == Node.ELEMENT_NODE) {
				System.out.println("\nNode Name = " + tempNode.getNodeName()+" [OPEN]");
				System.out.println("Node Value = "+tempNode.getTextContent());
				if(tempNode.hasAttributes()){
					NamedNodeMap nodeMap = tempNode.getAttributes();
					for(int i = 0; i < nodeMap.getLength(); i++){
						Node node = nodeMap.item(i);
						System.out.println("attr name : "+node.getNodeName());
						System.out.println("attr value : "+node.getNodeValue());
					}
				}
				if (tempNode.hasChildNodes()){
					printNode(tempNode.getChildNodes());
					
				}
				System.out.println("Node Name =" + tempNode.getNodeName()+ " [CLOSE]");
			}
		}
	}
}