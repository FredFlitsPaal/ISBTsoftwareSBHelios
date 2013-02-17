using System;

namespace Shared.Datastructures
{
	/// <summary>
	/// Graph met maximaal 16 nodes, dit maakt het mogelijk om een snellere 2log te maken
	/// </summary>
	class Graph16 : Graph
	{
		/// <summary>
		/// De knopen van de graaf
		/// </summary>
		int[] data;

		public Graph16(int size)
		{
			if (size > 16)
				throw new ArgumentOutOfRangeException("size", "size can not be bigger than 64");
			data = new int[size];
		}


		public override bool this[int x, int y]
		{
			get { return (data[x] & (1 << y)) != 0; }
			set
			{
				if (value)
					data[x] |= 1 << y;
				else
					data[x] &= ~(1 << y);
			}
		}

		public override void AddUndirectedEdge(int node0, int node1)
		{
			data[node0] |= 1 << node1;
			data[node1] |= 1 << node0;
		}

		public override void RemoveUndirectedEdge(int node0, int node1)
		{
			data[node0] &= ~(1 << node1);
			data[node1] &= ~(1 << node0);
		}

		public override bool HasUnevenSubgraph()
		{
			//in het begin mag je alle nodes nog bezoeken
			//dus alle nodes die echt bestaan krijgen een 1
			//de rest zijn "vullers" om de 32 bits vol te maken
			int undiscoveredNodes = ushort.MaxValue >> (16 - data.Length);

			while (undiscoveredNodes != 0)
			{
				//maak de laagste 1bit 0
				int newUndiscovered = undiscoveredNodes & (undiscoveredNodes - 1);
				//in node is maar 1 bit 1, namelijk de bit
				//die 0 geworden is in de regel hierboven
				int node = newUndiscovered ^ undiscoveredNodes;
				undiscoveredNodes = newUndiscovered;

				if ((countSubgraph(ref undiscoveredNodes, node) & 1) == 1)
					return true;
			}

			return false;
		}

		/// <summary>
		/// Telt het aantal nodes in een deelgraaf van een node
		/// </summary>
		/// <param name="undiscovered"></param>
		/// <param name="node"></param>
		/// <returns></returns>
		private int countSubgraph(ref int undiscovered, int node)
		{
			//local copy om accesses via een ref te voorkomen
			int undis = undiscovered;
			//dit is zegmaar "de stack", nodes waar je nog naar moet kijken
			//hebben een 1 in mayDiscover
			int mayDiscover = node;
			int count = 0;

			//special case - alle nodes zijn al bezocht dus deze
			//subgraph is maar 1 groot
			if (undis == 0)
				return 1;

			while (mayDiscover != 0)
			{
				//zelfde truc als in HasUnevenSubgraph
				//reset de laagste 1bit en pak daarna die bit
				int newMayDiscover = mayDiscover & (mayDiscover - 1);
				int discovered = newMayDiscover ^ mayDiscover;
				mayDiscover = newMayDiscover;
				count++;

				//bereken de index die correspondeert met de mask
				// O(1) operatie
				int index = BinaryLogarithm.log2((ushort)discovered);
				//voeg undiscovered nodes toe waarnaar node[index] een edge heeft
				mayDiscover |= (data[index] & undis);
				//kleur alle nodes die gezien zijn
				undis &= ~mayDiscover;
			}

			undiscovered = undis;
			return count;
		}

		public override int CountEdges(int node)
		{
			uint bits = (uint)(data[node] & (~(-1 << data.Length) & 0xFFFF));
			bits = bits - ((bits >> 1) & 0x55555555);
			bits = (bits & 0x33333333) + ((bits >> 2) & 0x33333333);
			return (int)(((bits + (bits >> 4) & 0xF0F0F0F) * 0x1010101) >> 24);
		}

		public override int Count
		{
			get { return data.Length; }
		}

		public override void Fill()
		{
			for (int i = 0; i < data.Length; i++)
				data[i] = 0xFFFF;
		}

		public static void Test()
		{
			Random r = new Random();
			const int graphSize = 16;
			Graph16 g16 = new Graph16(graphSize);
			Graph g = new BitGraph(graphSize);

			for (int j = 0; j < graphSize; j++)
			{
				int x = r.Next(0, graphSize);
				int y = r.Next(0, graphSize);
				g.AddUndirectedEdge(x, y);
				g16.AddUndirectedEdge(x, y);
			}

			for (int i = 0; i < 100000; i++)
			{
				for (int j = 0; j < graphSize / 4; j++)
				{
					int x = r.Next(0, graphSize);
					int y = r.Next(0, graphSize);

					if (r.NextDouble() > 0.90)
					{
						g.AddUndirectedEdge(x, y);
						g16.AddUndirectedEdge(x, y);
					}
					else
					{
						g.RemoveUndirectedEdge(x, y);
						g16.RemoveUndirectedEdge(x, y);
					}
				}

				for (int y = 0; y < graphSize; y++)
				{
					for (int x = 0; x < graphSize; x++)
					{
						if (g[x, y] != g16[x, y])
							throw new Exception();
					}
				}

				bool b;

				if ((b = g.HasUnevenSubgraph()) != g16.HasUnevenSubgraph())
					throw new Exception();

				Console.WriteLine(i + " " + b);
			}

		}
	}
}
