using System;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een geoptimaliseerde graaf implementatie die maximaal 64 knopen bevat
	/// </summary>
	[Serializable]
	class Graph64 : Graph
	{
		/// <summary>
		/// De knopen
		/// </summary>
		ulong[] data;

		/// <summary>
		/// Maakt een nieuwe Graph64 met size knopen (maximaal 64)
		/// </summary>
		/// <param name="size">Het aantal knopen in de Graph64 (maximaal 64)</param>
		public Graph64(int size)
		{
			if (size > 64)
				throw new ArgumentOutOfRangeException("size", "size can not be bigger than 64");
			data = new ulong[size];
		}

		/// <summary>
		/// Het aantal knopen in de Graph
		/// </summary>
		public override int Count
		{
			get { return data.Length; }
		}

		/// <summary>
		/// Voegt een ongerichte kant toe tussen node0 en node1
		/// </summary>
		public override void AddUndirectedEdge(int node0, int node1)
		{
			data[node0] |= 1ul << node1;
			data[node1] |= 1ul << node0;
		}

		/// <summary>
		/// Haalt de kant(en) tussen node0 en node1 weg
		/// </summary>
		public override void RemoveUndirectedEdge(int node0, int node1)
		{
			data[node0] &= ~(1ul << node1);
			data[node1] &= ~(1ul << node0);
		}

		public override bool this[int x, int y]
		{
			get { return (data[x] & (1ul << y)) != 0; }
			set
			{
				if (value)
					data[x] |= 1ul << y;
				else
					data[x] &= ~(1ul << y);
			}
		}

		/// <summary>
		/// Maakt alle mogelijke edges aan
		/// </summary>
		public override void Fill()
		{
			for (int i = 0; i < data.Length; i++)
				data[i] = ulong.MaxValue;
		}

		/// <summary>
		/// Test of de Graph samenhangende subgraphs heeft met een oneven aantal knopen
		/// </summary>
		/// <returns>true als de Graph minstens 1 subgraph met oneven aantal knopen heeft</returns>
		public override bool HasUnevenSubgraph()
		{
			//in het begin mag je alle nodes nog bezoeken
			//dus alle nodes die echt bestaan krijgen een 1
			//de rest zijn "vullers" om de 64 bits vol te maken
			ulong undiscoveredNodes = ulong.MaxValue >> (64 - data.Length);

			while (undiscoveredNodes != 0)
			{
				//maak de laagste 1bit 0
				ulong newUndiscovered = undiscoveredNodes & (undiscoveredNodes - 1);
				//in node is maar 1 bit 1, namelijk de bit
				//die 0 geworden is in de regel hierboven
				ulong node = newUndiscovered ^ undiscoveredNodes;
				undiscoveredNodes = newUndiscovered;
				if ((countSubgraph(ref undiscoveredNodes, node) & 1) == 1)
					return true;
			}

			return false;
		}

		/// <summary>
		/// Telt het aantal knopen in de deelgraaf die de node 'node' bevat
		/// </summary>
		/// <param name="undiscovered">De nodes die je nog mag bezoeken</param>
		/// <param name="node">De node van waaruit je begint te zoeken</param>
		/// <returns>Aantal knopen in de deelgraaf waar 'node' in zit</returns>
		int countSubgraph(ref ulong undiscovered, ulong node)
		{
			//local copy om accesses via een ref te voorkomen
			ulong undis = undiscovered;
			//dit is zegmaar "de stack", nodes waar je nog naar moet kijken
			//hebben een 1 in mayDiscover
			ulong mayDiscover = node;
			int count = 0;

			//special case - alle nodes zijn al bezocht dus deze
			//subgraph is maar 1 groot
			if (undis == 0)
				return 1;

			while (mayDiscover != 0)
			{
				//zelfde truc als in HasUnevenSubgraph
				//reset de laagste 1bit en pak daarna die bit
				ulong newMayDiscover = mayDiscover & (mayDiscover - 1);
				ulong discovered = newMayDiscover ^ mayDiscover;
				mayDiscover = newMayDiscover;
				count++;

				//bereken de index die correspondeert met de mask
				int index = BinaryLogarithm.log2(discovered);
				//voeg undiscovered nodes toe waarnaar node[index] een edge heeft
				mayDiscover |= (data[index] & undis);
				//kleur alle nodes die gezien zijn
				undis &= ~mayDiscover;
			}

			undiscovered = undis;
			return count;
		}

		public static void Test()
		{
			Random r = new Random();
			const int graphsize = 64;
			Graph64 g64 = new Graph64(graphsize);
			Graph g = new BitGraph(graphsize);

			for (int j = 0; j < graphsize; j++)
			{
				int x = r.Next(0, graphsize);
				int y = r.Next(0, graphsize);
				g.AddUndirectedEdge(x, y);
				g64.AddUndirectedEdge(x, y);
			}

			for (int i = 0; i < 100000; i++)
			{
				for (int j = 0; j < graphsize / 4; j++)
				{
					int x = r.Next(0, graphsize);
					int y = r.Next(0, graphsize);

					if (r.NextDouble() > 0.90)
					{
						g.AddUndirectedEdge(x, y);
						g64.AddUndirectedEdge(x, y);
					}
					else
					{
						g.RemoveUndirectedEdge(x, y);
						g64.RemoveUndirectedEdge(x, y);
					}
				}

				for (int y = 0; y < graphsize; y++)
				{
					for (int x = 0; x < graphsize; x++)
					{
						if (g[x, y] != g64[x, y])
							throw new Exception();
					}
				}

				bool b;

				if ((b = g.HasUnevenSubgraph()) != g64.HasUnevenSubgraph())
					throw new Exception();

				if ((i & 63) == 0)
					Console.WriteLine(i + " " + b);
			}

		}

		public override int CountEdges(int node)
		{
			int i = 0;
			ulong bits = data[node] & ~(ulong)(-1L << data.Length);

			for (; bits != 0; i++)
				bits &= bits - 1;

			return i;
		}
	}
}
