package hu.tilos.radio.backend;

import hu.radio.tilos.model.type.MixCategory;
import hu.radio.tilos.model.type.MixType;
import hu.tilos.radio.backend.converters.MappingFactory;
import hu.tilos.radio.backend.data.BookmarkSimple;
import hu.tilos.radio.backend.data.types.MixData;
import hu.tilos.radio.backend.data.types.MixSimple;
import org.jglue.cdiunit.AdditionalClasses;
import org.jglue.cdiunit.CdiRunner;
import org.junit.Assert;
import org.junit.BeforeClass;
import org.junit.Test;
import org.junit.runner.RunWith;

import javax.inject.Inject;
import javax.persistence.EntityManagerFactory;
import java.util.List;

import static org.junit.Assert.*;

@RunWith(CdiRunner.class)
@AdditionalClasses({MappingFactory.class, TestUtil.class})
public class BookmarkControllerTest {

    private static EntityManagerFactory factory;

    @Inject
    BookmarkController controller;

    @BeforeClass
    public static void setUp() throws Exception {
        factory = TestUtil.initPersistence();
        TestUtil.inidTestData();
    }

    @Test
    public void testList() {
        //given

        //when
        List<BookmarkSimple> responses = controller.list(null);

        //then
        Assert.assertEquals(2, responses.size());
    }


}