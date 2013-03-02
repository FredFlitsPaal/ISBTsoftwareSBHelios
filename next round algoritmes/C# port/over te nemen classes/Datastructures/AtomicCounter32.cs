using System.Threading;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een atomaire counter voor 32 bit integers
	/// </summary>
	public class AtomicCounter32
	{
		/// <summary>
		/// De waarde van de counter
		/// </summary>
		private int value = 0;

		/// <summary>
		/// Default constructor
		/// </summary>
		public AtomicCounter32() { }

		/// <summary>
		/// Constructor die een initiele waarde meekrijgt
		/// </summary>
		/// <param name="initialValue"> De initiele waarde </param>
		public AtomicCounter32(int initialValue)
		{
			value = initialValue;
		}

		/// <summary>
		/// Hoogt de waarde van de counter op met 1
		/// </summary>
		/// <returns> De nieuwe waarde van de counter</returns>
		public int Increment()
		{
			return Interlocked.Increment(ref value);
		}

		/// <summary>
		/// De waarde van de counter
		/// </summary>
		public int Value
		{
			get { return value; }
		}
	}
}
