using System;
using System.Collections;
using System.Collections.Generic;
using System.Diagnostics;
using System.Text;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een ongerichte graaf
	/// </summary>
	[DebuggerTypeProxy(typeof(DebuggerProxy)), DebuggerDisplay("{Count} nodes")]
    public abstract class Graph
	{

		/// <summary>
		/// Kiest de beste specializatie van Graph gebaseerd op het aantal nodes
		/// </summary>
		/// <param name="size">Het aantal nodes in de Graph</param>
		/// <returns>De Graph</returns>
		public static Graph Create(int size)
		{
			if (size < 16)
				return new Graph16(size);
			if (size < 32)
				return new Graph32(size);
			if (size < 64)
				return new Graph64(size);
			if (size < 128)
				return new Graph128(size);
			return new BitGraph(size);
		}

		/// <summary>
		/// Supertrage manier om een graaf te maken van een QuickSave string, alleeb voor debugging!
		/// </summary>
		/// <param name="quicksave">Een QuickSave string zoals je kan zien als je een graaf debugt</param>
		public static Graph CreateFromQuickSave(string quicksave)
		{
			string q = quicksave.TrimStart(':');
			int numberOfSizeChars = quicksave.Length - q.Length;
			string sizeString = q.Substring(0, numberOfSizeChars);
			q = q.Substring(numberOfSizeChars);
			int size = Convert.ToInt32(sizeString, 16);
			Graph result = Create(size);
			uint bits = 0;
			int numbits = 0;
			int x = 0;

			for (int i = 0; i < size; i++)
			{
				for (int j = i + 1; j < size; j++)
				{
					if (numbits == 0)
					{
						string bitstring = q.Substring(x, Math.Min(8, q.Length - x));
						bitstring += "00000000".Substring(0, 8 - bitstring.Length);
						bits = Convert.ToUInt32(bitstring, 16);
						numbits = 32;
						x += 8;
					}

					numbits--;
					if ((bits & 0x80000000) != 0)
						result.AddUndirectedEdge(i, j);

					bits <<= 1;
				}
			}

			return result;
		}

		/// <summary>
		/// De edge tussen node x en y
		/// </summary>
		/// <param name="x"> node x</param>
		/// <param name="y"> node y</param>
		/// <returns> bool representatie van de edge</returns>
		public abstract bool this[int x, int y] { get; set; }

		/// <summary>
		/// Voegt een ongerichte kant toe tussen node0 en node1
		/// </summary>
		public abstract void AddUndirectedEdge(int node0, int node1);

		/// <summary>
		/// Haalt de kant(en) tussen node0 en node1 weg
		/// </summary>
		public abstract void RemoveUndirectedEdge(int node0, int node1);

		/// <summary>
		/// Test of de Graph samenhangende subgraphs heeft met een oneven aantal knopen
		/// </summary>
		/// <returns>true als de Graph minstens 1 subgraph met oneven aantal knopen heeft</returns>
		public abstract bool HasUnevenSubgraph();

		/// <summary>
		/// Telt het aantal edges lopend vanaf een gegeven node.
		/// </summary>
		/// <param name="node">De node waarvoor het aantal edges geteld moet worden.</param>
		/// <returns>Het aantal edges vanaf de node.</returns>
		public abstract int CountEdges(int node);

		/// <summary>
		/// Controleert op een toekomstige deadlock iv.m. enkele verbinding tussen deelgrafen.
		/// </summary>
		/// <returns>True als dit in de volgende ronde tot een deadlock leidt.</returns>
		public bool LeadsToUnevenSubgraph()
		{
            //Een graaf leidt in de volgende ronde tot een deadlock als hij bestaat uit
            //2 delen die beide een oneven aantal knopen hebben, met maar 1 edge tussen deze 2 delen

            //Ga elke edge langs
			for (int i = 0; i < Count; i++)
			{
				for (int j = i + 1; j < Count; j++)
				{
					if (this[i, j])
					{
                        //Probeer de edge weg te halen en kijk of je dan 2 oneven deelgraven hebt
						RemoveUndirectedEdge(i, j);
						bool res = HasUnevenSubgraph();
						AddUndirectedEdge(i, j);

						if (res)
							return true;
					}
				}
			}
			return false;
		}

        /// <summary>
        /// Controleert of deze graaf leidt tot het soort graaf van LeadsToUnevenSubgraph()
        /// en hierdoor over 2 ronden tot een deadlock leidt
        /// </summary>
        /// <returns> True als dit over 2 ronden tot een deadlock leidt</returns>
        public bool LeadsToSingleConnectionGraph()
        {
            //Een graaf leidt over 2 ronden tot een deadlock als hij bestaat uit 2 delen 
            //die beide een oneven aantal knopen hebben, met 2 edges tussen deze 2 delen

            //Ga elke edge langs
            for (int i = 0; i < Count; i++)
            {
                for (int j = i + 1; j < Count; j++)
                {
                    if (this[i, j])
                    {
                        //Probeer de edge weg te halen en kijk dan of je een graaf hebt zoals in
                        //LeadsToUnevenSubGraph
                        RemoveUndirectedEdge(i, j);
                        bool res = LeadsToUnevenSubgraph();
                        AddUndirectedEdge(i, j);
                        if (res)
                            return true;
                    }
                }
            }
            return false;
        }

		/// <summary>
		/// Het aantal knopen in de Graph
		/// </summary>
		public abstract int Count { get; }

		/// <summary>
		/// Maakt alle mogenlijk edges aan
		/// </summary>
		public abstract void Fill();

		/// <summary>
		/// Bereken een lijst met subgraphs
		/// </summary>
		public List<BitArray> GetSubgraphs()
		{
			List<BitArray> subGraphs = new List<BitArray>();

			BitArray colours = new BitArray(Count);
			for (int i = 0; i < Count; i++)
			{
				if (!colours[i])
				{
					subGraphs.Add(DFS(i, colours));
				}
			}

			return subGraphs;
		}

        /// <summary>
        /// Doet een DFS vanuit 1 node over de graaf en returnt de nodes die gevonden worden
        /// </summary>
        /// <param name="team"> het startpunt </param>
        /// <param name="colours"> De "kleuren" die zijn toegekend aan nodes door eerdere DFS</param>
        /// <returns></returns>
		BitArray DFS(int team, BitArray colours)
		{
			Stack<int> stack = new Stack<int>(16);
			stack.Push(team);
			BitArray subGraph = new BitArray(Count);
			colours[team] = true;
			subGraph[team] = true;

			while (stack.Count > 0)
			{
				int node = stack.Pop();
				for (int i = 0; i < Count; i++)
				{
					if (this[i, node] && !colours[i])
					{
						stack.Push(i);
						colours[i] = true;
						subGraph[i] = true;
					}
				}
			}
			return subGraph;
		}

		/// <summary>
		/// Serialiseert de graaf naar een string
		/// </summary>
		/// <returns> Een string-representatie van de graaf </returns>
		public string ToQuickSave()
		{

			StringBuilder sb = new StringBuilder();

			string hex = string.Format("{0:x}", Count);
			for (int i = 0; i < hex.Length; i++)
				sb.Append(":");

			sb.Append(hex);
			int bitcount = 0;
			uint bits = 0;

			for (int i = 0; i < Count; i++)
			{
				for (int j = i + 1; j < Count; j++)
				{
					bitcount++;
					bits <<= 1;

					if (this[i, j])
						bits++;

					if (bitcount == 32)
					{
						sb.AppendFormat("{0:x8}", bits);
						bitcount = 0;
						bits = 0;
					}
				}
			}

			if (bitcount > 0)
				sb.Append(string.Format("{0:x8}", bits).TrimEnd('0'));

			return sb.ToString();
		}

		/// <summary>
		/// Een class die de debugger gebruikt om de Graph te benaderen
		/// </summary>
		private class DebuggerProxy
		{
			Graph graph;

			public DebuggerProxy(Graph g)
			{
				graph = g;
			}

			[DebuggerBrowsable(DebuggerBrowsableState.Collapsed)]
			public bool[,] Edges
			{
				get
				{
					bool[,] data = new bool[graph.Count, graph.Count];
					for (int i = 0; i < graph.Count; i++)
					{
						for (int j = 0; j < graph.Count; j++)
						{
							data[i, j] = graph[i, j];
						}
					}
					return data;
				}
			}

			[DebuggerDisplay("{x}, {y}")]
			public struct edge
			{
				[DebuggerBrowsable(DebuggerBrowsableState.Never)]
				int x, y;
				public edge(int x, int y)
				{
					this.x = x;
					this.y = y;
				}
			}

			[DebuggerDisplay("Count = {FilteredEdges.Length}")]
			public edge[] FilteredEdges
			{
				get
				{
					List<edge> edges = new List<edge>();
					for (int i = 0; i < graph.Count; i++)
					{
						for (int j = i + 1; j < graph.Count; j++)
						{
							if (graph[i, j])
								edges.Add(new edge(i, j));
						}
					}
					return edges.ToArray();
				}
			}

			public string QuickSave
			{
				get
				{
					//StringBuilder sb = new StringBuilder();

					//string hex = string.Format("{0:x}", graph.Count);
					//for (int i = 0; i < hex.Length; i++)
					//    sb.Append(":");

					//sb.Append(hex);
					//int bitcount = 0;
					//uint bits = 0;

					//for (int i = 0; i < graph.Count; i++)
					//{
					//    for (int j = i + 1; j < graph.Count; j++)
					//    {
					//        bitcount++;
					//        bits <<= 1;

					//        if (graph[i, j])
					//            bits++;

					//        if (bitcount == 32)
					//        {
					//            sb.AppendFormat("{0:x8}", bits);
					//            bitcount = 0;
					//            bits = 0;
					//        }
					//    }
					//}

					//if (bitcount > 0)
					//    sb.Append(string.Format("{0:x8}", bits).TrimEnd('0'));

					//return sb.ToString();
					return graph.ToQuickSave();
				}
			}

			public bool HasUnevenSubgraph
			{
				get { return graph.HasUnevenSubgraph(); }
			}
			public bool LeadsToUnevenSubgraph
			{
				get { return graph.LeadsToUnevenSubgraph(); }
			}

			public bool LeadsToSingleConnectionGraph
			{
				get { return graph.LeadsToSingleConnectionGraph(); }
			}
		}
	}

	/// <summary>
	/// Een bit-array implementatie van Graph.
	/// Wordt alleen gebruikt als de andere implementaties niet groot genoeg zijn
	/// </summary>
    class BitGraph : Graph
    {
        BitArray bits;
        int width;

        /// <summary>
        /// Maakt een Graph met <paramref name="size"/> nodes
        /// </summary>
        /// <param name="size">Het aantal nodes in de Graph</param>
        public BitGraph(int size)
        {
            width = size;
            bits = new BitArray(size * size);
        }

        public override bool this[int x, int y]
        {
            get { return bits[x + width * y]; }
            set { bits[x + width * y] = value; }
        }

        /// <summary>
        /// Het aantal knopen in de Graph
        /// </summary>
		public override int Count
		{
			get { return width; }
		}

        /// <summary>
        /// Voegt een dubbele edge toe aan de Graph tussen node0 en node1, de volgorde maakt dus niet uit
        /// </summary>
        public override void AddUndirectedEdge(int node0, int node1)
        {
            this[node0, node1] = true;
            this[node1, node0] = true;
        }

        /// <summary>
        /// Haalt alle edges die node0 en node1 verbinden weg (dus 0, 1 of 2 edges)
        /// </summary>
        public override void RemoveUndirectedEdge(int node0, int node1)
        {
            this[node0, node1] = false;
            this[node1, node0] = false;
        }

        /// <summary>
        /// Berekent met behulp van een DepthFirstSearch of er minstens 1 oneven deelgraaf is
        /// </summary>
        /// <returns>True als er minstens 1 oneven deelgraaf is</returns>
        public override bool HasUnevenSubgraph()
        {
            BitArray colours = new BitArray(width);

            for (int i = 0; i < width; i++)
            {
                if (!colours[i])
                {
                    if ((DFS(i, colours) & 1) == 1)
                        return true;
                }
            }
            return false;
        }

        /// <summary>
        /// Voert een Depth First Search uit vanuit de knoop 'team' en telt het aantal knopen
        /// </summary>
        /// <param name="team">Beginknoop voor de depth first search</param>
        /// <param name="colours">Bezocht/onbezocht array</param>
        /// <returns>Het aantal knopen in de deelgraaf met knoop 'team' er in</returns>
        int DFS(int team, BitArray colours)
        {
            int count = 0;
            Stack<int> stack = new Stack<int>(16);
            stack.Push(team);
            colours[team] = true;

            while (stack.Count > 0)
            {
                int node = stack.Pop();
                count++;

                for (int i = 0; i < width; i++)
                {
                    if (this[i, node] && !colours[i])
                    {
                        stack.Push(i);
                        colours[i] = true;
                    }
                }
            }

            return count;
        }

        internal static void Test()
        {
            Graph g = new BitGraph(10);

            g.AddUndirectedEdge(0, 8);
			g.AddUndirectedEdge(0, 4);
			g.AddUndirectedEdge(0, 9);
			g.AddUndirectedEdge(1, 8);

			g.AddUndirectedEdge(1, 9);
			g.AddUndirectedEdge(1, 4);
			g.AddUndirectedEdge(2, 5);
			g.AddUndirectedEdge(2, 6);

			g.AddUndirectedEdge(2, 7);
			g.AddUndirectedEdge(3, 5);
			g.AddUndirectedEdge(3, 6);
			g.AddUndirectedEdge(3, 7);

			g.AddUndirectedEdge(4, 9);
			g.AddUndirectedEdge(5, 6);
			g.AddUndirectedEdge(7, 8);
			//g.AddUndirectedEdge(0, 8);
            //g.AddUndirectedEdge(1, 8);
            //g.AddUndirectedEdge(1, 3);
            //g.AddUndirectedEdge(1, 2);
            //g.AddUndirectedEdge(2, 11);
            //g.AddUndirectedEdge(2, 12);
            //g.AddUndirectedEdge(3, 6);
            //g.AddUndirectedEdge(3, 8);
            //g.AddUndirectedEdge(4, 7);
            //g.AddUndirectedEdge(4, 10);
            //g.AddUndirectedEdge(5, 8);
            //g.AddUndirectedEdge(5, 9);
            //g.AddUndirectedEdge(6, 9);
            //g.AddUndirectedEdge(7, 10);



            if (!g.LeadsToUnevenSubgraph())
                throw new Exception("Uneven check failure");
        }

		/// <summary>
		/// Maakt alle mogenlijk edges aan
		/// </summary>
		public override void Fill()
		{
			bits.SetAll(true);
		}

		public override int CountEdges(int node)
		{
			int count = 0;
			for (int i = 0; i < width; i++)
			{
				if (this[node, i])
					count++;
			}
			return count;
		}
	}
}
