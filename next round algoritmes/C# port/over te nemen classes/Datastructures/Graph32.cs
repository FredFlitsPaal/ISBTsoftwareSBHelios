using System;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een geoptimaliseerde graaf implementatie die maximaal 32 knopen bevat
	/// </summary>
	[Serializable]
	class Graph32 : Graph
	{
		/// <summary>
		/// De knopen
		/// </summary>
		uint[] data;

		/// <summary>
		/// Maakt een nieuwe Graph32met size knopen (maximaal 32)
		/// </summary>
		/// <param name="size">Het aantal knopen in de Graph32 (maximaal 32)</param>
		public Graph32(int size)
		{
			if (size > 32)
				throw new ArgumentOutOfRangeException("size", "size can not be bigger than 64");
			data = new uint[size];
		}

		/// <summary>
		/// Voegt een ongerichte kant toe tussen node0 en node1
		/// </summary>
		public override void AddUndirectedEdge(int node0, int node1)
		{
			data[node0] |= 1u << node1;
			data[node1] |= 1u << node0;
		}

		/// <summary>
		/// Haalt de kant(en) tussen node0 en node1 weg
		/// </summary>
		public override void RemoveUndirectedEdge(int node0, int node1)
		{
			data[node0] &= ~(1u << node1);
			data[node1] &= ~(1u << node0);
		}

		public override bool this[int x, int y]
		{
			get { return (data[x] & (1u << y)) != 0; }
			set
			{
				if (value)
					data[x] |= 1u << y;
				else
					data[x] &= ~(1u << y);
			}
		}

		/// <summary>
		/// Test of de Graph samenhangende subgraphs heeft met een oneven aantal knopen
		/// </summary>
		/// <returns>true als de Graph minstens 1 subgraph met oneven aantal knopen heeft</returns>
		public override bool HasUnevenSubgraph()
		{
			//in het begin mag je alle nodes nog bezoeken
			//dus alle nodes die echt bestaan krijgen een 1
			//de rest zijn "vullers" om de 32 bits vol te maken
			uint undiscoveredNodes = uint.MaxValue >> (32 - data.Length);

			while (undiscoveredNodes != 0)
			{
				//maak de laagste 1bit 0
				uint newUndiscovered = undiscoveredNodes & (undiscoveredNodes - 1);
				//in node is maar 1 bit 1, namelijk de bit
				//die 0 geworden is in de regel hierboven
				uint node = newUndiscovered ^ undiscoveredNodes;
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
		int countSubgraph(ref uint undiscovered, uint node)
		{
			//local copy om accesses via een ref te voorkomen
			uint undis = undiscovered;
			//dit is zegmaar "de stack", nodes waar je nog naar moet kijken
			//hebben een 1 in mayDiscover
			uint mayDiscover = node;
			int count = 0;

			//special case - alle nodes zijn al bezocht dus deze
			//subgraph is maar 1 groot
			if (undis == 0)
				return 1;

			while (mayDiscover != 0)
			{
				//zelfde truc als in HasUnevenSubgraph
				//reset de laagste 1bit en pak daarna die bit
				uint newMayDiscover = mayDiscover & (mayDiscover - 1);
				uint discovered = newMayDiscover ^ mayDiscover;
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

		/// <summary>
		/// Maakt alle mogenlijk edges aan
		/// </summary>
		public override void Fill()
		{
			for (int i = 0; i < data.Length; i++)
				data[i] = uint.MaxValue;
		}

		/// <summary>
		/// Het aantal knopen in de Graph
		/// </summary>
		public override int Count
		{
			get { return data.Length; }
		}

		public static void Test()
		{
			Random r = new Random();
			const int graphSize = 32;
			Graph32 g32 = new Graph32(graphSize);
			Graph g = new BitGraph(graphSize);

			for (int j = 0; j < graphSize; j++)
			{
				int x = r.Next(0, graphSize);
				int y = r.Next(0, graphSize);
				g.AddUndirectedEdge(x, y);
				g32.AddUndirectedEdge(x, y);
			}

			for (int i = 0; i < 5000; i++)
			{
				for (int j = 0; j < graphSize / 4; j++)
				{
					int x = r.Next(0, graphSize);
					int y = r.Next(0, graphSize);

					if (r.NextDouble() > 0.90)
					{
						g.AddUndirectedEdge(x, y);
						g32.AddUndirectedEdge(x, y);
					}
					else
					{
						g.RemoveUndirectedEdge(x, y);
						g32.RemoveUndirectedEdge(x, y);
					}
				}

				for (int y = 0; y < graphSize; y++)
				{
					for (int x = 0; x < graphSize; x++)
					{
						if (g[x, y] != g32[x, y])
							throw new Exception();
					}
				}

				bool b;
				if ((b = g.HasUnevenSubgraph()) != g32.HasUnevenSubgraph())
					throw new Exception();

				Console.WriteLine(i + " " + b);
			}

		}

		public override int CountEdges(int node)
		{
			uint bits = data[node] & ~(uint)(-1 << data.Length);
			bits = bits - ((bits >> 1) & 0x55555555);
			bits = (bits & 0x33333333) + ((bits >> 2) & 0x33333333);
			return (int)(((bits + (bits >> 4) & 0xF0F0F0F) * 0x1010101) >> 24);
		}
	}
}
