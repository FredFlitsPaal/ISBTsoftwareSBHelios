using System;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een geoptimaliseerde graaf implementatie die maximaal 128 knopen bevat
	/// </summary>
	class Graph128 : Graph
	{
		/// <summary>
		/// De knopen en hun edges
		/// </summary>
		UInt128[] data;

		public Graph128(int size)
		{
			if (size > 128)
				throw new ArgumentOutOfRangeException("size", "size can not be bigger than 128");
			data = new UInt128[size];
		}

		/// <summary>
		/// Dit is een implementatie van een 128bits unsigned integer
		/// die net genoeg bevat om de graph algoritmes te draaien.
		/// Bevat dus geen operatoren voor echt rekenwerk.
		/// Alle operatoren die er wél in zitten, zijn O(1).
		/// Zelfs de bitshift operatoren (behalve op super oude CPU's)
		/// </summary>
		private struct UInt128
		{
			/// <summary>
			/// 2 ulongs die samen de 128-bit integer representeren
			/// </summary>
			ulong low, high;

			/// <summary>
			/// Representatie van de maximum waarde
			/// </summary>
			public static readonly UInt128 MaxValue = (UInt128)(-1);
			/// <summary>
			/// Representatie van het getal 1
			/// </summary>
			public static readonly UInt128 One = new UInt128(1);

			/// <summary>
			/// Maakt een Uint128 met een ulong.
			/// </summary>
			/// <param name="value"> de waarde </param>
			public UInt128(ulong value)
			{
				low = value;
				high = 0;
			}

			/// <summary>
			/// Maakt een Uint128 met 2 ulongs
			/// </summary>
			/// <param name="low"> De lage ulong </param>
			/// <param name="high"> De hoge ulong</param>
			public UInt128(ulong low, ulong high)
			{
				this.low = low;
				this.high = high;
			}

			/// <summary>
			/// Berekent de 2-log
			/// </summary>
			/// <returns> De 2-log van de UInt128</returns>
			public int log2()
			{
				if (high == 0)
					return BinaryLogarithm.log2(low);

				return 64 + BinaryLogarithm.log2(high);
			}

			/// <summary>
			/// Is de UInt128 gelijk aan 0
			/// </summary>
			public bool IsZero { get { return (low | high) == 0; } }

			/// <summary>
			/// Is de UInt128 niet gelijk aan 0
			/// </summary>
			public bool IsNotZero { get { return (low | high) != 0; } }

			/// <summary>
			/// Cast een ulong naar een UInt128
			/// </summary>
			/// <param name="value"> de ulong</param>
			/// <returns></returns>
			public static implicit operator UInt128(ulong value)
			{
				return new UInt128(value);
			}

			/// <summary>
			/// Cast een int naar een Uint128
			/// </summary>
			/// <param name="value"> de int </param>
			/// <returns></returns>
			public static explicit operator UInt128(int value)
			{
				long val = (long)value;
				return new UInt128((ulong)val, (ulong)(val >> 63)/* sign extension */);
			}

			public override string ToString()
			{
				return high.ToString("X16") + low.ToString("X16");
			}

			/// <summary>
			/// Leftshift operator
			/// </summary>
			/// <param name="x"></param>
			/// <param name="n"></param>
			/// <returns></returns>
			public static UInt128 operator <<(UInt128 x, int n)
			{
				if (n >= 64)
				{
					n -= 64;
					x.high = x.low;
					x.low = 0;
				}

				if (n == 0)
					return x;

				x.high = (x.high << n) | (x.low >> (64 - n));
				x.low <<= n;
				return x;
			}
			/// <summary>
			/// Rightshift operator
			/// </summary>
			/// <param name="x"></param>
			/// <param name="n"></param>
			/// <returns></returns>
			public static UInt128 operator >>(UInt128 x, int n)
			{
				if (n >= 64)
				{
					n -= 64;
					x.low = x.high;
					x.high = 0;
				}

				if (n == 0)
					return x;

				x.low = (x.low >> n) | (x.high << (64 - n));
				x.high >>= n;
				return x;
			}

			/// <summary>
			/// Bitwise or
			/// </summary>
			/// <param name="a"></param>
			/// <param name="b"></param>
			/// <returns></returns>
			public static UInt128 operator |(UInt128 a, UInt128 b)
			{
				a.low |= b.low;
				a.high |= b.high;
				return a;
			}

			/// <summary>
			/// Bitwise and
			/// </summary>
			/// <param name="a"></param>
			/// <param name="b"></param>
			/// <returns></returns>
			public static UInt128 operator &(UInt128 a, UInt128 b)
			{
				a.high &= b.high;
				a.low &= b.low;
				return a;
			}

			/// <summary>
			/// Bitwise negatie
			/// </summary>
			/// <param name="x"></param>
			/// <returns></returns>
			public static UInt128 operator ~(UInt128 x)
			{
				x.low = ~x.low;
				x.high = ~x.high;
				return x;
			}

			/// <summary>
			/// Bitwise XOR
			/// </summary>
			/// <param name="a"></param>
			/// <param name="b"></param>
			/// <returns></returns>
			public static UInt128 operator ^(UInt128 a, UInt128 b)
			{
				a.high ^= b.high;
				a.low ^= b.low;
				return a;
			}

			/// <summary>
			/// aftrek operator
			/// </summary>
			/// <param name="x"></param>
			/// <param name="n"></param>
			/// <returns></returns>
			public static UInt128 operator -(UInt128 x, ulong n)
			{
				UInt128 result = x;
				result.low -= n;

				if (result.low > x.low)	//borrow
					result.high--;

				return result;
			}
		}

		public override bool this[int x, int y]
		{
			get { return (data[x] & (UInt128.One << y)).IsNotZero; }
			set
			{
				if (value)
					data[x] |= UInt128.One << y;
				else
					data[x] &= ~(UInt128.One << y);
			}
		}

		public override void AddUndirectedEdge(int node0, int node1)
		{
			data[node0] |= UInt128.One << node1;
			data[node1] |= UInt128.One << node0;
		}

		public override void RemoveUndirectedEdge(int node0, int node1)
		{
			data[node0] &= ~(UInt128.One << node1);
			data[node1] &= ~(UInt128.One << node0);
		}

		public override bool HasUnevenSubgraph()
		{
			//in het begin mag je alle nodes nog bezoeken
			//dus alle nodes die echt bestaan krijgen een 1
			//de rest zijn "vullers" om de 128 bits vol te maken
			UInt128 undiscoveredNodes = UInt128.MaxValue >> (128 - data.Length);

			while (undiscoveredNodes.IsNotZero)
			{
				//maak de laagste 1bit 0
				UInt128 newUndiscovered = undiscoveredNodes & (undiscoveredNodes - 1);
				//in node is maar 1 bit 1, namelijk de bit
				//die 0 geworden is in de regel hierboven
				UInt128 node = newUndiscovered ^ undiscoveredNodes;
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
		private int countSubgraph(ref UInt128 undiscovered, UInt128 node)
		{
			//local copy om accesses via een ref te voorkomen
			UInt128 undis = undiscovered;
			//dit is zegmaar "de stack", nodes waar je nog naar moet kijken
			//hebben een 1 in mayDiscover
			UInt128 mayDiscover = node;
			int count = 0;

			//special case - alle nodes zijn al bezocht dus deze
			//subgraph is maar 1 groot
			if (undis.IsZero)
				return 1;

			while (mayDiscover.IsNotZero)
			{
				//zelfde truc als in HasUnevenSubgraph
				//reset de laagste 1bit en pak daarna die bit
				UInt128 newMayDiscover = mayDiscover & (mayDiscover - 1);
				UInt128 discovered = newMayDiscover ^ mayDiscover;
				mayDiscover = newMayDiscover;
				count++;

				//bereken de index die correspondeert met de mask
				int index = discovered.log2();
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
			int i = 0;
			UInt128 bits = data[node] & ~(UInt128.MaxValue << data.Length);

			for (; bits.IsNotZero; i++)
				bits &= bits - 1;

			return i;
		}

		public override int Count
		{
			get { return data.Length; }
		}

		public override void Fill()
		{
			for (int i = 0; i < data.Length; i++)
				data[i] = UInt128.MaxValue;
		}


		public static void Test()
		{
			Random r = new Random();
			const int graphsize = 128;
			Graph128 g128 = new Graph128(graphsize);
			Graph g = new BitGraph(graphsize);

			for (int j = 0; j < graphsize; j++)
			{
				int x = r.Next(0, graphsize);
				int y = r.Next(0, graphsize);
				g.AddUndirectedEdge(x, y);
				g128.AddUndirectedEdge(x, y);
			}

			for (int i = 0; i < 256; i++)
			{
				for (int j = 0; j < graphsize / 4; j++)
				{
					int x = r.Next(0, graphsize);
					int y = r.Next(0, graphsize);

					if (r.NextDouble() > 0.90)
					{
						g.AddUndirectedEdge(x, y);
						g128.AddUndirectedEdge(x, y);
					}
					else
					{
						g.RemoveUndirectedEdge(x, y);
						g128.RemoveUndirectedEdge(x, y);
					}
				}

				for (int y = 0; y < graphsize; y++)
				{
					for (int x = 0; x < graphsize; x++)
					{
						if (g[x, y] != g128[x, y])
							throw new Exception();
					}
				}

				bool b;

				if ((b = g.HasUnevenSubgraph()) != g128.HasUnevenSubgraph())
					throw new Exception();

				if ((i & 7) == 0)
					Console.WriteLine(i + " " + b);
			}

		}
	}
}
