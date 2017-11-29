# SolrSearchEngine
A search engine using Apache Solr on webpages collected by crawling over CNN website. The search algorithms used are Lucene and PageRank.
Spell Correction, Auto Completion and Text Snippets are also added and their detailed description is given in Report.pdf.

Can test and run at :
http://54.245.69.202/search.php

Steps followed :

1. Install Solr 6.5.0

2. In Ubuntu terminal, change directory to solr-6.x.x and start Solr using command : bin/solr start

3. Create a core using command : bin/solr create -c mycore . Here 'mycore' is my core's name.

4. Modify managed-schema in conf folder in solr/mycore so that all the text content from html pages extracted by Tika are mapped correctly.

5. Now we will perform indexing using command : bin/post -c mycore <path_to_folder> . We will give the path to the folder containing html files related to our news site.

6. Open the solr UI from address : http://localhost:8983/solr/ . Here, you can see statistics related to your indexed files.

7. Solr by default uses Lucene to query the search results. To use Pagerank, we need an EdgeList file containing edges connecting two files. We create this file using jsoup from our mapCNNData file given to us. Thus,it delivers a web graph to us.

8. Now, we will compute Pagerank scores for all our links using Networkx library in python. Here, the score defining command is :

pr=nx.pagerank(G,alpha=0.85,personalization=None,max_iter=30,tol=1e-06,nstart=None,weight='weight',dangling=None)

Here,:
G is our directed graph computed from edgeList file.
alpha is Damping parameter for Pagerank, we are using default value =0.85.
Personalization refers to a personalization vector assignining a key and a non-zero value to each node. We have used a default uniform distribution.
max_iter refers to maximum number of iterations in our power iteration method. We have set it to 30.
tol referst to Error tolerance which is used to check convergence in power method solver.
nstart refers to a dictionary that assigns a starting value of Pagerank iteration for each node.
Weight assigns the edge data key that will be used as weight.
dangling refers to a dictionary to assign outgoing edges and corresponding weights to those “dangling” nodes which have no outgoing edges. We have set it to 'None' thus it uses our personalization value which again is 'None' and therefore uses uniform distribution.

9. Add the external pageRankFile field into our managed-schema.
   
10. Modify the solrconfig file to include listeners.

11. Clone the php client situated at : https://github.com/PTCInc/solr-php-client.git

12. Edit the php client to generate my custom UI which includes a textbox for my query, radio buttons to select Lucene or Pagerank and a button to submit the query.

13. Search the given 8 queries and save top 10 results for each Lucene and Pagerank algorithm.

How the search works :
1. User enters the query and selects whether he wants to use Lucene or Pagerank to search and submits the query.
2. Query is formatted by server and sent to solr. Solr on the basis of algorithm selected uses Lucene or Pagerank scores from external pageRankFile to return the search results in JSON.
3. Server again reformates the result returned in JSON format and presents to user.

Why Pagerank of some pages is higher than others?
Pagerank uses the property that if the number of inlinks to a page is higher than the Pagerank value will be higher. Thus, the page with a higher Pagerank values is more important. Number of inlinks to some pages, in the web graph that we generated from our edgeList file, is higher than others and that's why their Pagerank value is high.

New properties added are :
1. Spell Correction
2.AutoCompletion
3. Text Snippet

Detailed description is given in Report.pdf

