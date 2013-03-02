using System;
using System.Threading;

namespace Shared.Datastructures
{
	/// <summary>
	/// Een atomaire counter voor 64 bit integers
	/// </summary>
	public class AtomicCounter
	{
		/// <summary>
		/// Waarde van de counter
		/// </summary>
		private long value = 0;
		/// <summary>
		/// Mutex om atomiciteit mee te bewaren
		/// </summary>
		private Mutex mutex = new Mutex();

		/// <summary>
		/// Default constructor
		/// </summary>
		public AtomicCounter() { }

		/// <summary>
		/// Constructor met een initiele waarde
		/// </summary>
		/// <param name="initialValue"> De initiele waarde</param>
		public AtomicCounter(long initialValue)
		{
			value = initialValue;
		}

		/// <summary>
		/// Vraag de waarde van de counter op
		/// </summary>
		/// <returns> De waarde van de counter</returns>
		public long GetValue()
		{
			long tempvalue;
			mutex.WaitOne();
			tempvalue = value;
			mutex.ReleaseMutex();
			return tempvalue;
		}

		/// <summary>
		/// Vraag de waarde van de counter op en hoog hem op met 1
		/// </summary>
		/// <returns> De waarde van de counter </returns>
		public long IncreaseAndGetValue()
		{
			long tempvalue;
			mutex.WaitOne();
			value++;
			tempvalue = value;
			mutex.ReleaseMutex();
			return tempvalue;
		}

		/// <summary>
		/// Hoog de counter op met 1
		/// </summary>
		public void IncreaseValue()
		{
			mutex.WaitOne();
			value++;
			mutex.ReleaseMutex();
		}

		
		////klein voorstel - geen zwaar geschut zoals een Mutex nodig voor
		////een countertje, daar hebben we Interlocked voor
		//public long GetValueAtomic()
		//{
		//    return Interlocked.Read(ref value);
		//}

		//public long IncrementAndGetAtomic()
		//{
		//    return Interlocked.Increment(ref value);
		//}

		//public void IncrementAtomic()
		//{
		//    Interlocked.Increment(ref value);
		//}
	}
}
