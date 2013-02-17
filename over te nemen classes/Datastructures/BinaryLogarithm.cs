namespace Shared.Datastructures
{
	/// <summary>
	/// Klasse die een 2-log kan uitrekenen
	/// </summary>
	static class BinaryLogarithm
	{

		/// <summary>
		/// Tabel met alle 2-logs
		/// </summary>
		static sbyte[] logtab;

		/// <summary>
		/// Initialiseert de log-tabel
		/// </summary>
		static BinaryLogarithm()
		{
			logtab = new sbyte[0x10000];

			// bereken de logaritmes of alle 16bit unsigned getallen
			for (int i = 2; i < logtab.Length; i++)
				logtab[i] = (sbyte)(logtab[i >> 1] + 1);

			logtab[0] = -1;
		}

		/// <summary>
		/// Berekent de 2log van x
		/// </summary>
		public static int log2(ushort x)
		{
			return logtab[x];
		}

		/// <summary>
		/// Berekent de 2log van x
		/// </summary>
		public static int log2(uint x)
		{
			if (x <= 0xFFFF)
				return logtab[x];
			else
				return 16 + logtab[x >> 16];
		}

		/// <summary>
		/// Berekent de 2log van x
		/// </summary>
		public static int log2(ulong x)
		{
			if (x <= 0xFFFFFFFF)
			{
				if (x <= 0xFFFF)
					return logtab[x];
				else
					return 16 + logtab[x >> 16];
			}
			else
			{
				x >>= 32;
				if (x <= 0xFFFF)
					return 32 + logtab[x];
				else
					return 48 + logtab[x >> 16];
			}
		}
	}
}
