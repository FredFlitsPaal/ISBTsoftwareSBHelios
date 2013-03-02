using System;

namespace Shared.Datastructures
{
	/// <summary>
	/// Klasse met methoden om bools en integers uit XML te halen
	/// </summary>
	public static class TypeFunctions
	{
		/// <summary>
		/// Zet een bool om naar een XML string
		/// </summary>
		/// <param name="obj"> een bool </param>
		/// <returns> XML representatie van de bool </returns>
		public static String ToXMLString(this bool obj)
		{
			return obj ? "1" : "0";
		}

		/// <summary>
		/// Zet een XML string om naar een bool
		/// </summary>
		/// <param name="obj"> De XML string </param>
		/// <returns> De bool die de srting representeert </returns>
		public static bool ToBool(this string obj)
		{
			try
			{
				if (obj == "") return false;
				if (obj == "0") return false;
				if (obj.ToLower().Trim().StartsWith("f")) return false;
				return true;
			}
			catch
			{
				return false;
			}
		}

		/// <summary>
		/// Zet een XML string om naar een 64 bit integer
		/// </summary>
		/// <param name="obj"> De XML string </param>
		/// <returns> De 64 integer die de string representeert </returns>
		public static long ToInt64(this string obj)
		{
			if (obj.Length == 0)
				return 0;
			long result;
			if (long.TryParse(obj, out result))
				return result;
			return 0;
		}

		/// <summary>
		/// Zet een XML string om naar een 64 bit integer
		/// </summary>
		/// <param name="obj"> De XML string </param>
		/// <returns> De 64 integer die de string representeert </returns>
		public static Int32 ToInt32(this string obj)
		{
			if (obj.Length == 0)
				return 0;
			int result;
			if (int.TryParse(obj, out result))
				return result;
			return 0;
		}

		/// <summary>
		/// Zet een XML string om naar een 64 bit integer
		/// </summary>
		/// <param name="obj"> De XML string </param>
		/// <returns> De 64 integer die de string representeert </returns>
		public static Int16 ToInt16(this string obj)
		{
			if (obj.Length == 0)
				return 0;
			short result;
			if (short.TryParse(obj, out result))
				return result;
			return 0;
		}
	}
}
