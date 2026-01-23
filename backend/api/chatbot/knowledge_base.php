<?php
/**
 * St. Lawrence School AI Assistant - Knowledge Base
 * This file contains all the information the chatbot can answer
 */

class KnowledgeBase {
    
    private $knowledge = [];
    
    public function __construct() {
        $this->buildKnowledgeBase();
    }
    
    private function buildKnowledgeBase() {
        
        // SCHOOL INFORMATION
        $this->knowledge['school_info'] = [
            'keywords' => ['about', 'school', 'information', 'who are you', 'tell me about', 'history', 'founded', 'established', 'years', 'experience', 'old', 'age', 'since when', 'how long', 'premier', 'quality'],
            'response' => "St. Lawrence Junior School - Kabowa is a premier mixed day and boarding primary school offering quality education since 2010. With over 14 years of experience, we provide a nurturing environment where excellence meets innovation. Our school offers both nursery and primary education with modern facilities and experienced teachers dedicated to your child's success. We are located in Kabowa, Kampala, and serve families from across the city."
        ];
        
        // YEARS OF EXPERIENCE
        $this->knowledge['experience'] = [
            'keywords' => ['years of experience', 'how long', 'established', 'founded', 'since when', 'how old', 'age of school', 'experience', 'years in operation'],
            'response' => "🎓 **Years of Experience:**\n\nSt. Lawrence Junior School - Kabowa was established in 2010. We have been providing quality education for over 14 years! Our experience includes:\n\n✅ 14+ years of academic excellence\n✅ Hundreds of successful graduates\n✅ Consistent excellent PLE results\n✅ Award-winning programs\n✅ Trusted by families across Kampala\n\nOur long-standing reputation is built on dedication, quality teaching, and student success!"
        ];
        
        // CONTACT INFORMATION
        $this->knowledge['contact'] = [
            'keywords' => ['contact', 'phone', 'email', 'address', 'location', 'reach', 'call', 'telephone', 'where are you', 'phone number', 'email address', 'physical address', 'find you', 'located', 'where is', 'kabowa', 'kampala', 'rubaga', 'church zone'],
            'response' => "📍 **Contact Information:**\n\n📧 Email: stlawrencejuniorschoolkabowa@gmail.com\n📧 Alternative: st.lawrence.juniorschool@yahoo.com\n📞 Phone: +256 701 420 506\n📞 Phone: +256 772 420 506\n📮 P.O. Box: 36198, Kampala\n📍 Location: Kabowa, Church Zone, Rubaga Division - Kampala District, Uganda\n\nFeel free to reach out to us anytime!"
        ];
        
        // LOCATION DETAILS
        $this->knowledge['location'] = [
            'keywords' => ['where', 'location', 'address', 'find', 'directions', 'how to get', 'where located', 'situated', 'place', 'area', 'zone', 'division', 'district', 'kabowa', 'rubaga', 'church zone'],
            'response' => "📍 **School Location:**\n\n🏫 St. Lawrence Junior School - Kabowa\n📍 Kabowa, Church Zone\n📍 Rubaga Division\n📍 Kampala District, Uganda\n\n📮 Postal Address: P.O. Box 36198, Kampala\n\n📞 For directions, call us:\n+256 701 420 506\n+256 772 420 506\n\nWe are easily accessible from Kampala city center!"
        ];
        
        // SCHOOL HOURS
        $this->knowledge['hours'] = [
            'keywords' => ['hours', 'time', 'open', 'close', 'schedule', 'timing', 'when do you open', 'operating hours', 'what time', 'opening time', 'closing time', 'school time', 'start time', 'end time', 'working hours', 'office hours'],
            'response' => "⏰ **School Hours:**\n\nMonday - Friday: 7:00 AM - 5:00 PM\nSaturday: 8:00 AM - 1:00 PM (Sports & Extra Activities)\nSunday: Closed\n\nOffice Hours: 8:00 AM - 4:00 PM (Weekdays)\n\nFor boarding students, we provide 24/7 supervision and care."
        ];
        
        // ADMISSION PROCESS
        $this->knowledge['admission'] = [
            'keywords' => ['admission', 'apply', 'enroll', 'join', 'register', 'application', 'how to apply', 'enrollment', 'admissions'],
            'response' => "📝 **Admission Process:**\n\n1. **Visit Our School** - Schedule a tour to see our facilities\n2. **Obtain Application Form** - Available at the school office or download from our website\n3. **Submit Documents:**\n   - Birth certificate\n   - Passport photos (2)\n   - Previous school report (if applicable)\n   - Immunization card\n4. **Interview & Assessment** - Meet with our admissions team\n5. **Receive Admission Letter**\n6. **Pay Fees & Complete Registration**\n\nAdmissions are open throughout the year! Visit our Admission page for more details or call us at +256 701 420 506."
        ];
        
        // SCHOOL FEES - COMPLETE (DAY & BOARDING)
        $this->knowledge['fees_complete'] = [
            'keywords' => ['fees', 'cost', 'tuition', 'price', 'how much', 'payment', 'school fees', 'tuition fees', 'pay', 'charges', 'amount', 'money', 'expensive', 'cheap', 'affordable', 'pricing', 'fee structure'],
            'response' => "💰 **School Fees Structure (Per Term):**\n\n**DAY SCHOLARS:**\n🎒 Nursery (Baby - Top Class): UGX 474,000\n📚 P1 - P5: UGX 579,000\n🎓 P6 - P7: UGX 629,000\n\n**BOARDING:**\n🏠 Nursery (Baby - Top Class): UGX 894,000\n📚 P1 - P5: UGX 1,019,000\n🎓 P6 - P7: UGX 1,094,000\n\n**Payment:** Per term (3 terms per year)\n\nDay scholar fees include tuition, meals, and learning materials. Boarding fees include accommodation, meals, 24/7 supervision, and all learning materials. Our boarding facilities are modern and secure!"
        ];
        
        // SCHOOL FEES - DAY SCHOLARS ONLY
        $this->knowledge['fees_day'] = [
            'keywords' => ['day scholar fees', 'day student fees', 'day scholar cost', 'day scholar price', 'day scholar tuition', 'non-boarding fees'],
            'response' => "💰 **School Fees (Day Scholars - Per Term):**\n\n🎒 **Nursery (Baby - Top Class):** UGX 474,000\n📚 **P1 - P5:** UGX 579,000\n🎓 **P6 - P7:** UGX 629,000\n\n**Payment:** Per term (3 terms per year)\n\nFees include tuition, meals, and learning materials. For boarding fees, please ask!"
        ];
        
        // SCHOOL FEES - BOARDING ONLY
        $this->knowledge['fees_boarding'] = [
            'keywords' => ['boarding fees', 'boarding cost', 'boarding price', 'boarding student', 'boarder', 'boarding tuition', 'boarding charges'],
            'response' => "💰 **School Fees (Boarding - Per Term):**\n\n🏠 **Nursery (Baby - Top Class):** UGX 894,000\n📚 **P1 - P5:** UGX 1,019,000\n🎓 **P6 - P7:** UGX 1,094,000\n\n**Payment:** Per term (3 terms per year)\n\nBoarding fees include accommodation, meals, 24/7 supervision, and all learning materials. Our boarding facilities are modern and secure with separate houses for boys and girls!"
        ];
        
        // UNIFORMS
        $this->knowledge['uniforms'] = [
            'keywords' => ['uniform', 'dress code', 'clothing', 'attire', 'what to wear', 'uniforms', 'dress', 'clothes', 'outfit', 'school uniform', 'uniform price', 'uniform cost', 'uniform fees'],
            'response' => "👔 **School Uniforms:**\n\n**Day Scholars (Boys):**\n- Nur-P2: UGX 200,000\n- P3-P5: UGX 240,000\n- P6-P7: UGX 300,000\n\n**Day Scholars (Girls):**\n- Nur-P2: UGX 190,000\n- P3-P5: UGX 230,000\n- P6-P7: UGX 270,000\n\n**Boarding Students:** Slightly higher (includes extra sets)\n\nUniforms include shirts/blouses, shorts/skirts, sweater, socks, and school tie. Available for purchase at the school."
        ];
        
        // PROGRAMS OFFERED
        $this->knowledge['programs'] = [
            'keywords' => ['programs', 'classes', 'levels', 'grades', 'what do you offer', 'curriculum'],
            'response' => "📚 **Programs Offered:**\n\n🎨 **Nursery Section:**\n- Baby Class\n- Middle Class\n- Top Class\n\n📖 **Primary Section:**\n- Primary 1 - Primary 7\n\n**Options:**\n✅ Day Scholars\n✅ Boarding (Full boarding facilities)\n\nWe follow the Ugandan National Curriculum with enhanced learning programs including computer studies, music, sports, and arts."
        ];
        
        // EXTRACURRICULAR ACTIVITIES
        $this->knowledge['activities'] = [
            'keywords' => ['activities', 'extracurricular', 'sports', 'clubs', 'games', 'music', 'drama', 'what activities'],
            'response' => "🎯 **Extracurricular Activities:**\n\n⚽ **Sports:**\n- Football\n- Netball\n- Athletics\n- Swimming\n\n🎨 **Arts & Culture:**\n- Music & Dance\n- Drama & Theatre\n- Art & Crafts\n\n📚 **Academic Clubs:**\n- Debate Club\n- Science Club\n- Reading Club\n- Computer Club\n\n🏆 **Competitions:**\n- Inter-school sports\n- Music festivals\n- Academic competitions\n\nWe believe in holistic education - developing both mind and body!"
        ];
        
        // FACILITIES
        $this->knowledge['facilities'] = [
            'keywords' => ['facilities', 'infrastructure', 'buildings', 'library', 'computer lab', 'playground', 'what do you have'],
            'response' => "🏫 **Our Facilities:**\n\n📚 **Library** - Well-stocked with books and digital resources\n💻 **Computer Lab** - Modern computers with internet\n🔬 **Science Lab** - Equipped for practical learning\n🏃 **Sports Grounds** - Football field, netball court\n🏠 **Boarding Houses** - Separate for boys and girls\n🍽️ **Dining Hall** - Nutritious meals prepared daily\n🚌 **Transport** - School buses available\n🏥 **Sick Bay** - First aid and medical care\n🎨 **Art Room** - Creative learning space\n\nAll facilities are modern, safe, and well-maintained!"
        ];
        
        // TEACHERS
        $this->knowledge['teachers'] = [
            'keywords' => ['teachers', 'staff', 'instructors', 'educators', 'qualified', 'experienced'],
            'response' => "👨‍🏫 **Our Teachers:**\n\nWe have a team of highly qualified and experienced teachers who are passionate about education. Our staff includes:\n\n✅ Certified teachers with degrees and diplomas\n✅ Specialized subject teachers\n✅ Experienced nursery care providers\n✅ Sports coaches and activity coordinators\n✅ Guidance and counseling staff\n\nOur teacher-to-student ratio ensures personalized attention for every child. Visit our Teachers page to meet our dedicated team!"
        ];
        
        // LIBRARY
        $this->knowledge['library'] = [
            'keywords' => ['library', 'books', 'reading', 'study materials', 'resources'],
            'response' => "📚 **School Library:**\n\nOur library is a treasure trove of knowledge with:\n\n📖 Over 2,000 books covering various subjects\n📰 Newspapers and magazines\n💻 Digital resources and e-books\n🪑 Quiet study areas\n👥 Group discussion spaces\n\n**Library Hours:**\nMonday - Friday: 8:00 AM - 4:00 PM\nSaturday: 9:00 AM - 12:00 PM\n\nStudents can borrow books and access research materials. We encourage a culture of reading!"
        ];
        
        // MEALS
        $this->knowledge['meals'] = [
            'keywords' => ['meals', 'food', 'lunch', 'breakfast', 'dinner', 'nutrition', 'cafeteria', 'dining'],
            'response' => "🍽️ **Meals & Nutrition:**\n\n**Day Scholars:**\n- Mid-morning snack\n- Lunch\n\n**Boarding Students:**\n- Breakfast\n- Mid-morning snack\n- Lunch\n- Afternoon snack\n- Dinner\n\nAll meals are:\n✅ Nutritious and balanced\n✅ Prepared by trained cooks\n✅ Served in a clean dining hall\n✅ Supervised by staff\n\nWe cater to special dietary needs. Our menu is designed by nutritionists to support growing children!"
        ];
        
        // TRANSPORT
        $this->knowledge['transport'] = [
            'keywords' => ['transport', 'bus', 'school bus', 'pick up', 'drop off', 'transportation'],
            'response' => "🚌 **School Transport:**\n\nWe provide safe and reliable school transport services:\n\n✅ Modern school buses\n✅ Experienced drivers\n✅ Designated routes covering major areas\n✅ Morning pick-up and afternoon drop-off\n✅ Supervised by staff\n\n**Routes cover:**\n- Kampala Central\n- Nateete\n- Busega\n- Lubaga\n- And surrounding areas\n\nTransport fees are separate from tuition. Contact us for route details and pricing!"
        ];
        
        // SECURITY
        $this->knowledge['security'] = [
            'keywords' => ['security', 'safety', 'safe', 'protection', 'guards', 'secure'],
            'response' => "🔒 **Safety & Security:**\n\nYour child's safety is our top priority:\n\n✅ 24/7 security guards\n✅ CCTV surveillance\n✅ Controlled access gates\n✅ Visitor registration system\n✅ Fire safety equipment\n✅ First aid facilities\n✅ Emergency response procedures\n✅ Trained staff for child protection\n\nFor boarding students, we provide round-the-clock supervision. Our campus is fully fenced and secure!"
        ];
        
        // PARENT INVOLVEMENT
        $this->knowledge['parents'] = [
            'keywords' => ['parent', 'parents', 'involvement', 'meetings', 'communication', 'updates'],
            'response' => "👨‍👩‍👧‍👦 **Parent Involvement:**\n\nWe believe in strong parent-school partnerships:\n\n📅 **Parent-Teacher Meetings** - Every term\n📊 **Progress Reports** - Sent home regularly\n📱 **Communication** - Phone calls, SMS, WhatsApp groups\n🎉 **School Events** - Parents invited to participate\n👥 **PTA Meetings** - Active Parent-Teacher Association\n\nWe keep parents informed about their child's progress, behavior, and school activities. Your involvement is valued!"
        ];
        
        // TERM DATES
        $this->knowledge['term_dates'] = [
            'keywords' => ['term', 'semester', 'calendar', 'academic calendar', 'when does school start', 'holidays'],
            'response' => "📅 **Academic Calendar:**\n\nWe follow a 3-term academic year:\n\n**Term 1:** February - April\n**Term 2:** May - August\n**Term 3:** September - November\n\n**Holidays:**\n- December - January (Long holiday)\n- Short breaks between terms\n\nExact dates are communicated at the beginning of each year. Contact us for the current academic calendar!"
        ];
        
        // ACHIEVEMENTS
        $this->knowledge['achievements'] = [
            'keywords' => ['achievements', 'awards', 'performance', 'results', 'success', 'excellence'],
            'response' => "🏆 **Our Achievements:**\n\nWe're proud of our students' accomplishments:\n\n✅ Consistent excellent PLE results\n✅ Winners in inter-school competitions\n✅ Music and dance festival awards\n✅ Sports championships\n✅ Academic excellence awards\n✅ Community service recognition\n\nOur students have been admitted to top secondary schools in Uganda. We celebrate every child's unique talents and achievements!"
        ];
        
        // SPECIAL NEEDS
        $this->knowledge['special_needs'] = [
            'keywords' => ['special needs', 'disability', 'inclusive', 'learning difficulties', 'support'],
            'response' => "♿ **Special Needs Support:**\n\nWe are committed to inclusive education:\n\n✅ Individualized learning plans\n✅ Trained special needs teachers\n✅ Accessible facilities\n✅ Extra support for learning difficulties\n✅ Counseling services\n✅ Small class sizes for attention\n\nWe assess each child's needs and provide appropriate support. Please contact us to discuss your child's specific requirements!"
        ];
        
        // VISITING THE SCHOOL
        $this->knowledge['visit'] = [
            'keywords' => ['visit', 'tour', 'see the school', 'come to school', 'schedule a visit'],
            'response' => "🏫 **Visit Our School:**\n\nWe'd love to show you around!\n\n📞 **Schedule a Tour:**\nCall: +256 701 420 506 / +256 772 420 506\nEmail: stlawrencejuniorschoolkabowa@gmail.com\n\n**What to expect:**\n✅ Guided tour of facilities\n✅ Meet the headteacher\n✅ See classrooms in action\n✅ Ask questions\n✅ Get admission information\n\n**Best times to visit:**\nMonday - Friday: 9:00 AM - 3:00 PM\nSaturday: 9:00 AM - 12:00 PM\n\nNo appointment needed, but calling ahead ensures we're ready to welcome you!"
        ];
        
        // GREETING RESPONSES
        $this->knowledge['greeting'] = [
            'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'greetings'],
            'response' => "Hello! 👋 Welcome to St. Lawrence Junior School - Kabowa! I'm your virtual assistant, here to help you with any questions about our school. How can I assist you today?"
        ];
        
        // DETAILED LOCATION INFORMATION
        $this->knowledge['location_detailed'] = [
            'keywords' => ['where exactly', 'exact location', 'directions', 'how to get there', 'how to find', 'map', 'google maps', 'landmarks', 'nearby'],
            'response' => "📍 **Detailed Location Information:**\n\n**Address:**\nSt. Lawrence Junior School - Kabowa\nKabowa, Kampala\nP.O. BOX 36198, KAMPALA\nUganda\n\n**Area:** Kabowa is located in Rubaga Division, Kampala\n\n**Nearby Landmarks:**\n- Close to Kabowa Market\n- Near Nateete Road\n- Accessible from Kampala-Masaka Road\n\n**How to Get Here:**\n- From Kampala City: Take Nateete Road towards Busega, turn at Kabowa\n- Public Transport: Taxis to Nateete/Busega, then boda to Kabowa\n- Private Car: Ample parking available on campus\n\n**GPS Coordinates:** Available upon request\n\nFor specific directions, call us at +256 701 420 506 and we'll guide you!"
        ];
        
        // HEADTEACHER/PRINCIPAL INFORMATION
        $this->knowledge['headteacher'] = [
            'keywords' => ['headteacher', 'principal', 'head teacher', 'director', 'head of school', 'school head', 'who is in charge', 'school leader'],
            'response' => "👨‍💼 **School Leadership:**\n\nOur school is led by an experienced and dedicated headteacher who oversees all academic and administrative operations. Our leadership team includes:\n\n✅ **Headteacher** - Overall school management and academic excellence\n✅ **Deputy Headteacher** - Academic programs and curriculum\n✅ **Director of Studies** - Teaching quality and student performance\n✅ **Boarding Master/Mistress** - Boarding student welfare\n✅ **Discipline Master/Mistress** - Student conduct and behavior\n\nOur leadership team has decades of combined experience in education and is committed to providing the best learning environment for every child.\n\nTo schedule a meeting with the headteacher, call +256 701 420 506."
        ];
        
        // TEACHERS DETAILED INFORMATION
        $this->knowledge['teachers_detailed'] = [
            'keywords' => ['teachers', 'staff', 'instructors', 'educators', 'qualified', 'experienced', 'teaching staff', 'faculty', 'how many teachers', 'teacher qualifications', 'teacher experience'],
            'response' => "👨‍🏫 **Our Teaching Staff:**\n\nWe have a team of **30+ highly qualified teachers** who are passionate about education:\n\n**Qualifications:**\n✅ Bachelor's Degrees in Education\n✅ Diplomas in Primary Education\n✅ Specialized subject certifications\n✅ Early Childhood Education certificates\n✅ Continuous professional development\n\n**Experience:**\n✅ Average 8+ years teaching experience\n✅ Trained in modern teaching methods\n✅ Child psychology and development training\n✅ First aid certified\n\n**Specializations:**\n📚 **Subject Teachers:** Mathematics, English, Science, Social Studies\n🎨 **Special Teachers:** Music, Art, Physical Education, Computer Studies\n👶 **Nursery Teachers:** Early childhood specialists\n🏠 **Boarding Staff:** Matrons and patrons for boarding students\n📖 **Support Staff:** Librarian, Lab technician, Counselor\n\n**Teacher-Student Ratio:** 1:25 (ensures personalized attention)\n\nVisit our Teachers page on the website to meet our dedicated team!"
        ];
        
        // CONTACT PERSON INFORMATION
        $this->knowledge['contact_person'] = [
            'keywords' => ['who can i talk to', 'contact person', 'who to call', 'who to speak with', 'admissions officer', 'bursar', 'accountant', 'who handles'],
            'response' => "📞 **Key Contact Persons:**\n\n**For Admissions & Enrollment:**\n👤 Admissions Officer\n📞 +256 701 420 506 / +256 772 420 506\n📧 stlawrencejuniorschoolkabowa@gmail.com\n\n**For Fees & Payments:**\n👤 School Bursar/Accountant\n📞 +256 701 420 506\n⏰ Available: Mon-Fri 8AM-4PM\n\n**For Academic Matters:**\n👤 Director of Studies\n📞 +256 701 420 506\n📧 st.lawrence.juniorschool@yahoo.com\n\n**For Boarding Inquiries:**\n👤 Boarding Master/Mistress\n📞 +256 772 420 506\n\n**For General Inquiries:**\n👤 School Secretary\n📞 +256 701 420 506 / +256 772 420 506\n⏰ Office Hours: Mon-Fri 8AM-4PM\n\nAll staff are friendly and ready to assist you!"
        ];
        
        // SCHOOL EMAIL ADDRESSES
        $this->knowledge['email_addresses'] = [
            'keywords' => ['email', 'email address', 'send email', 'write to', 'contact email', 'official email'],
            'response' => "📧 **Official Email Addresses:**\n\n**Primary Email:**\nstlawrencejuniorschoolkabowa@gmail.com\n(For all general inquiries, admissions, and information)\n\n**Alternative Email:**\nst.lawrence.juniorschool@yahoo.com\n(For academic matters and official correspondence)\n\n**Response Time:**\n✅ We typically respond within 24 hours during weekdays\n✅ For urgent matters, please call: +256 701 420 506\n\n**What to Include in Your Email:**\n- Your name and contact information\n- Subject of inquiry (Admission, Fees, Programs, etc.)\n- Specific questions or information needed\n- Best time to call you back (if needed)\n\nWe look forward to hearing from you!"
        ];
        
        // PHONE NUMBERS DETAILED
        $this->knowledge['phone_numbers'] = [
            'keywords' => ['phone number', 'telephone', 'call', 'mobile', 'contact number', 'phone', 'telephone number', 'hotline'],
            'response' => "📱 **Contact Phone Numbers:**\n\n**Main Lines:**\n📞 +256 701 420 506 (MTN)\n📞 +256 772 420 506 (Airtel)\n\n**Available:**\n⏰ Monday - Friday: 7:00 AM - 5:00 PM\n⏰ Saturday: 8:00 AM - 1:00 PM\n⏰ Sunday: Closed (Emergency only)\n\n**What You Can Call About:**\n✅ Admissions and enrollment\n✅ School fees and payments\n✅ Academic programs and curriculum\n✅ Boarding facilities\n✅ School tours and visits\n✅ General inquiries\n✅ Emergency matters (24/7 for boarding parents)\n\n**Tips for Calling:**\n- Best time: 9AM-12PM or 2PM-4PM (weekdays)\n- Have your questions ready\n- Ask for specific department if needed\n- Request callback if lines are busy\n\nWe're always happy to hear from you!"
        ];
        
        // OFFICE LOCATION ON CAMPUS
        $this->knowledge['office_location'] = [
            'keywords' => ['office', 'administration', 'admin office', 'where is the office', 'reception', 'front desk'],
            'response' => "🏢 **Administration Office Location:**\n\n**Main Office:**\nLocated at the entrance of the school campus\nEasily accessible from the main gate\n\n**Office Hours:**\n⏰ Monday - Friday: 8:00 AM - 4:00 PM\n⏰ Saturday: 9:00 AM - 12:00 PM\n⏰ Sunday: Closed\n\n**Services Available:**\n✅ Admissions and enrollment\n✅ Fee payments and receipts\n✅ Student records and transcripts\n✅ General information\n✅ Complaint and suggestion box\n✅ Lost and found\n\n**Reception Staff:**\nOur friendly reception staff will welcome you and direct you to the appropriate department or person.\n\n**Visitor Procedure:**\n1. Report to reception/security at main gate\n2. Sign visitor's book\n3. State purpose of visit\n4. Receive visitor's badge\n5. Be directed to relevant office/person\n\nWalk-ins welcome during office hours!"
        ];
        
        // SOCIAL MEDIA & ONLINE PRESENCE
        $this->knowledge['social_media'] = [
            'keywords' => ['facebook', 'social media', 'instagram', 'twitter', 'whatsapp', 'online', 'website', 'social'],
            'response' => "🌐 **Connect With Us Online:**\n\n**Website:**\nwww.stlawrencejuniorschool.com (Coming soon!)\n\n**Email:**\n📧 stlawrencejuniorschoolkabowa@gmail.com\n📧 st.lawrence.juniorschool@yahoo.com\n\n**Phone/WhatsApp:**\n📱 +256 701 420 506\n📱 +256 772 420 506\n\n**Social Media:**\nWe're working on establishing our social media presence!\nFor now, the best way to reach us is:\n- Phone calls\n- WhatsApp messages\n- Email\n- Visit us in person\n\n**Stay Updated:**\nContact us to be added to our parent communication groups where we share:\n✅ School announcements\n✅ Event updates\n✅ Academic calendars\n✅ Important notices\n✅ Photo galleries\n\nCall +256 701 420 506 to stay connected!"
        ];
        
        // STAFF DEPARTMENTS
        $this->knowledge['staff_departments'] = [
            'keywords' => ['departments', 'sections', 'who handles what', 'staff structure', 'organization'],
            'response' => "🏫 **School Departments & Staff:**\n\n**Academic Department:**\n👨‍🏫 Headteacher - Overall leadership\n👨‍🏫 Deputy Headteacher - Academic programs\n👨‍🏫 Director of Studies - Curriculum & teaching\n👨‍🏫 Subject Teachers - Specialized instruction\n👨‍🏫 Class Teachers - Primary class management\n\n**Administration:**\n👤 School Secretary - Office management\n👤 Bursar/Accountant - Fees & finances\n👤 Receptionist - Visitor services\n\n**Student Welfare:**\n👤 Boarding Master/Mistress - Boarding students\n👤 Discipline Master/Mistress - Student conduct\n👤 School Counselor - Guidance & support\n👤 School Nurse - Health services\n\n**Support Services:**\n👤 Librarian - Library management\n👤 Lab Technician - Science lab\n👤 IT Coordinator - Computer lab\n👤 Sports Coach - Physical education\n👤 Music Teacher - Arts & music\n\n**Operations:**\n👤 Security Guards - Campus safety (24/7)\n👤 Cooks - Meal preparation\n👤 Cleaners - Facility maintenance\n👤 Drivers - School transport\n\nTotal Staff: 50+ dedicated professionals!"
        ];
        
        // THANK YOU RESPONSES
        $this->knowledge['thanks'] = [
            'keywords' => ['thank you', 'thanks', 'appreciate', 'grateful'],
            'response' => "You're very welcome! 😊 If you have any more questions about St. Lawrence Junior School, feel free to ask. We're here to help!"
        ];
        
        // GOODBYE RESPONSES
        $this->knowledge['goodbye'] = [
            'keywords' => ['bye', 'goodbye', 'see you', 'later', 'thanks bye'],
            'response' => "Goodbye! 👋 Thank you for your interest in St. Lawrence Junior School - Kabowa. We hope to see you soon! For more information, call us at +256 701 420 506."
        ];
    }
    
    public function findAnswer($question) {
        $question = strtolower(trim($question));
        
        // Score-based matching for better results
        $matches = [];
        
        // Check each knowledge entry
        foreach ($this->knowledge as $key => $data) {
            $score = 0;
            foreach ($data['keywords'] as $keyword) {
                $keyword = strtolower($keyword);
                // Exact match gets highest score
                if ($question === $keyword) {
                    $score += 100;
                }
                // Contains keyword gets good score
                if (strpos($question, $keyword) !== false) {
                    $score += 50;
                }
                // Keyword contains part of question
                if (strpos($keyword, $question) !== false && strlen($question) > 3) {
                    $score += 30;
                }
                // Word-by-word matching
                $questionWords = explode(' ', $question);
                $keywordWords = explode(' ', $keyword);
                foreach ($questionWords as $qWord) {
                    if (strlen($qWord) > 3) { // Skip short words
                        foreach ($keywordWords as $kWord) {
                            if (strtolower($qWord) === strtolower($kWord)) {
                                $score += 20;
                            }
                        }
                    }
                }
            }
            
            if ($score > 0) {
                $matches[$key] = [
                    'score' => $score,
                    'response' => $data['response'],
                    'category' => $key
                ];
            }
        }
        
        // Sort by score (highest first)
        uasort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Return best match if score is good enough
        if (!empty($matches)) {
            $bestMatch = reset($matches);
            if ($bestMatch['score'] >= 20) { // Minimum threshold
                return [
                    'found' => true,
                    'response' => $bestMatch['response'],
                    'category' => $bestMatch['category']
                ];
            }
        }
        
        // Default response if no match found
        return [
            'found' => false,
            'response' => "I'm not sure about that specific question. However, I can help you with:\n\n• School fees and payments\n• Admission process\n• Contact information\n• School programs and facilities\n• Extracurricular activities\n• And much more!\n\nPlease try asking in a different way, or contact us directly at +256 701 420 506.",
            'category' => 'unknown'
        ];
    }
    
    public function getQuickActions() {
        return [
            "What are your school hours?",
            "How do I apply for admission?",
            "What extracurricular activities do you offer?",
            "What is the school's contact information?",
            "What are the school fees?",
            "Do you offer boarding?"
        ];
    }
}
